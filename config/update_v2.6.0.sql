-- CRM Total - Database Update Script (MySQL 5.7 compatible)
-- Version: 2.6.0
-- Date: 2025-11-30
-- Description: Expediente Digital Afiliado (EDA) enhancements
--              - Add person_type (Persona Física / Persona Moral based on RFC)
--              - Full NIZA classification with OTRA CATEGORÍA option
--              - Enhanced upselling invitations with WhatsApp and email messaging

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- =============================================
-- PERSON TYPE (Persona Física vs Persona Moral)
-- =============================================

-- Add person_type column to contacts (auto-calculated from RFC)
-- RFC 13 chars = Persona Física (business owner)
-- RFC 12 chars = Persona Moral (legal representative)
ALTER TABLE `contacts` 
ADD COLUMN `person_type` ENUM('fisica', 'moral') NULL 
COMMENT 'Type based on RFC: fisica (13 chars, owner) or moral (12 chars, legal rep)'
AFTER `rfc`;

-- =============================================
-- UPDATE EXISTING CONTACTS WITH PERSON TYPE
-- =============================================

-- Set person_type based on RFC length for existing contacts
UPDATE `contacts` 
SET `person_type` = 'fisica' 
WHERE LENGTH(rfc) = 13 AND rfc IS NOT NULL AND rfc != '';

UPDATE `contacts` 
SET `person_type` = 'moral' 
WHERE LENGTH(rfc) = 12 AND rfc IS NOT NULL AND rfc != '';

-- =============================================
-- FULL NIZA CLASSIFICATION (45 Classes - Official Mexico)
-- =============================================

-- Clear existing classifications and insert complete official NIZA list
DELETE FROM `niza_classifications`;

-- NIZA Classes 1-45 - Official Mexico Classification
INSERT INTO `niza_classifications` (`class_number`, `name`, `description`, `keywords`) VALUES
('1', 'Productos químicos', 'Productos químicos para la industria, la ciencia y la fotografía, así como para la agricultura, la horticultura y la silvicultura; resinas artificiales en bruto, materias plásticas en bruto; abonos para el suelo; composiciones extintoras; preparaciones para el temple y la soldadura; sustancias químicas para conservar alimentos; materias curtientes; adhesivos para la industria.', '["química", "industrial", "fertilizantes", "adhesivos", "resinas"]'),
('2', 'Pinturas, barnices, lacas', 'Pinturas, barnices, lacas; productos antioxidantes y productos para conservar la madera; materias tintóreas; mordientes; resinas naturales en bruto; metales en hojas y en polvo para pintores, decoradores, impresores y artistas.', '["pintura", "barniz", "laca", "colorantes", "tintes"]'),
('3', 'Productos de limpieza y cosméticos', 'Preparaciones para blanquear y otras sustancias para lavar la ropa; preparaciones para limpiar, pulir, desengrasar y raspar; jabones; productos de perfumería, aceites esenciales, cosméticos, lociones capilares; dentífricos.', '["cosméticos", "limpieza", "jabón", "perfumes", "belleza"]'),
('4', 'Aceites y combustibles industriales', 'Aceites y grasas para uso industrial; lubricantes; productos para absorber, regar y asentar el polvo; combustibles (incluida la gasolina para motores) y materias de alumbrado; velas y mechas de iluminación.', '["aceites", "lubricantes", "combustibles", "gasolina", "velas"]'),
('5', 'Productos farmacéuticos', 'Productos farmacéuticos y veterinarios; productos higiénicos y sanitarios para uso médico; alimentos y sustancias dietéticas para uso médico o veterinario; complementos alimenticios para personas o animales; emplastos, material para apósitos; material para empastes e improntas dentales; desinfectantes; productos para eliminar animales dañinos; fungicidas, herbicidas.', '["farmacéuticos", "medicinas", "veterinarios", "sanitarios", "desinfectantes"]'),
('6', 'Metales comunes', 'Metales comunes y sus aleaciones; materiales de construcción metálicos; construcciones transportables metálicas; materiales metálicos para vías férreas; cables e hilos metálicos no eléctricos; artículos de cerrajería y ferretería metálicos; tubos y tuberías metálicos; cajas de caudales; productos metálicos no comprendidos en otras clases; minerales metalíferos.', '["metales", "construcción", "ferretería", "cerrajería", "acero"]'),
('7', 'Máquinas y máquinas herramientas', 'Máquinas y máquinas herramientas; motores (excepto motores para vehículos terrestres); acoplamientos y elementos de transmisión (excepto para vehículos terrestres); instrumentos agrícolas que no sean accionados manualmente; incubadoras de huevos; distribuidores automáticos.', '["máquinas", "motores", "herramientas", "industrial", "maquinaria"]'),
('8', 'Herramientas e instrumentos manuales', 'Herramientas e instrumentos de mano accionados manualmente; artículos de cuchillería, tenedores y cucharas; armas blancas; maquinillas de afeitar.', '["cuchillería", "herramientas", "navajas", "tijeras", "instrumentos"]'),
('9', 'Aparatos científicos y electrónicos', 'Aparatos e instrumentos científicos, náuticos, geodésicos, fotográficos, cinematográficos, ópticos, de pesaje, de medición, de señalización, de control (inspección), de salvamento y de enseñanza; aparatos e instrumentos de conducción, distribución, transformación, acumulación, regulación o control de la electricidad; aparatos de grabación, transmisión o reproducción de sonido o imágenes; soportes de registro magnéticos, discos acústicos; discos compactos, DVD y otros soportes de grabación digitales; mecanismos para aparatos de previo pago; cajas registradoras, máquinas de calcular, equipos de procesamiento de datos, ordenadores; software; extintores.', '["electrónica", "computadoras", "software", "tecnología", "audio", "video"]'),
('10', 'Aparatos médicos', 'Aparatos e instrumentos quirúrgicos, médicos, odontológicos y veterinarios, así como miembros, ojos y dientes artificiales; artículos ortopédicos; material de sutura.', '["médico", "quirúrgico", "dental", "ortopédico", "instrumentos médicos"]'),
('11', 'Aparatos de iluminación y calefacción', 'Aparatos de alumbrado, de calefacción, de producción de vapor, de cocción, de refrigeración, de secado, de ventilación, de distribución de agua e instalaciones sanitarias.', '["iluminación", "calefacción", "refrigeración", "ventilación", "sanitarios"]'),
('12', 'Vehículos', 'Vehículos; aparatos de locomoción terrestre, aérea o acuática.', '["vehículos", "automóviles", "transporte", "motos", "bicicletas"]'),
('13', 'Armas de fuego', 'Armas de fuego; municiones y proyectiles; explosivos; fuegos artificiales.', '["armas", "municiones", "explosivos", "pirotecnia"]'),
('14', 'Metales preciosos y joyería', 'Metales preciosos y sus aleaciones; artículos de joyería, bisutería, piedras preciosas; artículos de relojería e instrumentos cronométricos.', '["joyería", "relojes", "oro", "plata", "piedras preciosas"]'),
('15', 'Instrumentos musicales', 'Instrumentos musicales.', '["música", "instrumentos", "guitarra", "piano", "percusión"]'),
('16', 'Papel y artículos de papelería', 'Papel, cartón y artículos de estas materias no comprendidos en otras clases; productos de imprenta; artículos de encuadernación; fotografías; artículos de papelería; adhesivos (pegamentos) de papelería o para uso doméstico; material para artistas; pinceles; máquinas de escribir y artículos de oficina (excepto muebles); material de instrucción o material didáctico (excepto aparatos); materias plásticas para embalar (no comprendidas en otras clases); caracteres de imprenta; clichés de imprenta.', '["papelería", "imprenta", "papel", "oficina", "encuadernación"]'),
('17', 'Caucho y plásticos', 'Caucho, gutapercha, goma, amianto, mica y productos de estas materias no comprendidos en otras clases; productos en materias plásticas semielaboradas; materiales para calafatear, estopar y aislar; tubos flexibles no metálicos.', '["caucho", "plástico", "aislantes", "empaques", "mangueras"]'),
('18', 'Artículos de cuero', 'Cuero y cuero de imitación, y productos de estas materias no comprendidos en otras clases; pieles de animales; baúles y maletas; paraguas y sombrillas; bastones; fustas, arneses y artículos de guarnicionería.', '["cuero", "bolsas", "maletas", "carteras", "pieles"]'),
('19', 'Materiales de construcción no metálicos', 'Materiales de construcción no metálicos; tubos rígidos no metálicos para la construcción; asfalto, pez y betún; construcciones transportables no metálicas; monumentos no metálicos.', '["construcción", "cemento", "concreto", "asfalto", "ladrillos"]'),
('20', 'Muebles', 'Muebles, espejos, marcos; productos de madera, corcho, caña, junco, mimbre, cuerno, hueso, marfil, ballena, concha, ámbar, nácar, espuma de mar, sucedáneos de todas estas materias o de materias plásticas, no comprendidos en otras clases.', '["muebles", "madera", "decoración", "espejos", "marcos"]'),
('21', 'Utensilios domésticos', 'Utensilios y recipientes para uso doméstico y culinario; peines y esponjas; cepillos; materiales para fabricar cepillos; material de limpieza; lana de acero; vidrio en bruto o semielaborado (excepto el vidrio utilizado en la construcción); artículos de cristalería, porcelana y loza no comprendidos en otras clases.', '["utensilios", "cocina", "hogar", "cristalería", "porcelana"]'),
('22', 'Cuerdas y fibras textiles', 'Cuerdas, cordeles, redes, tiendas de campaña, toldos, velas de navegación, sacos y bolsas (no comprendidos en otras clases); materiales de acolchado y relleno (excepto de caucho o de materias plásticas); materias textiles fibrosas en bruto.', '["cuerdas", "textiles", "lonas", "tiendas", "fibras"]'),
('23', 'Hilos para uso textil', 'Hilos para uso textil.', '["hilos", "textil", "costura", "bordado"]'),
('24', 'Productos textiles', 'Tejidos y productos textiles no comprendidos en otras clases; ropa de cama; ropa de mesa.', '["telas", "textiles", "ropa de cama", "mantelería", "cortinas"]'),
('25', 'Prendas de vestir', 'Prendas de vestir, calzado, artículos de sombrerería.', '["ropa", "calzado", "vestimenta", "moda", "sombreros"]'),
('26', 'Mercería y accesorios', 'Encajes y bordados, cintas y cordones; botones, ganchos y ojetes, alfileres y agujas; flores artificiales.', '["mercería", "botones", "encajes", "bordados", "accesorios"]'),
('27', 'Alfombras y tapices', 'Alfombras, felpudos, esteras, linóleo y otros revestimientos de suelos; tapicerías murales que no sean de materias textiles.', '["alfombras", "tapetes", "pisos", "tapicería"]'),
('28', 'Juegos y juguetes', 'Juegos y juguetes; artículos de gimnasia y deporte no comprendidos en otras clases; decoraciones para árboles de Navidad.', '["juguetes", "deportes", "juegos", "gimnasia", "navidad"]'),
('29', 'Carne, pescado, productos lácteos', 'Carne, pescado, carne de ave y carne de caza; extractos de carne; frutas y verduras, hortalizas y legumbres en conserva, congeladas, secas y cocidas; jaleas, confituras, compotas; huevos; leche y productos lácteos; aceites y grasas comestibles.', '["alimentos", "carne", "lácteos", "conservas", "aceites comestibles"]'),
('30', 'Café, té, productos de panadería', 'Café, té, cacao y sucedáneos del café; arroz; tapioca y sagú; harinas y preparaciones a base de cereales; pan, productos de pastelería y de confitería; helados; azúcar, miel, jarabe de melaza; levadura, polvos de hornear; sal; mostaza; vinagre, salsas (condimentos); especias; hielo.', '["café", "panadería", "pastelería", "cereales", "condimentos"]'),
('31', 'Productos agrícolas y semillas', 'Granos y productos agrícolas, hortícolas y forestales, no comprendidos en otras clases; animales vivos; frutas y verduras, hortalizas y legumbres frescas; semillas; plantas y flores naturales; alimentos para animales; malta.', '["agricultura", "semillas", "plantas", "animales", "horticultura"]'),
('32', 'Bebidas no alcohólicas', 'Cervezas; aguas minerales y gaseosas, y otras bebidas sin alcohol; bebidas de frutas y zumos de frutas; siropes y otras preparaciones para elaborar bebidas.', '["bebidas", "refrescos", "agua", "cerveza", "jugos"]'),
('33', 'Bebidas alcohólicas', 'Bebidas alcohólicas (excepto cervezas).', '["vinos", "licores", "tequila", "mezcal", "destilados"]'),
('34', 'Tabaco y artículos para fumadores', 'Tabaco; artículos para fumadores; cerillas.', '["tabaco", "cigarros", "puros", "encendedores"]'),
('35', 'Publicidad y negocios', 'Publicidad; gestión de negocios comerciales; administración comercial; trabajos de oficina.', '["publicidad", "marketing", "administración", "negocios", "comercio"]'),
('36', 'Seguros y finanzas', 'Seguros; operaciones financieras; operaciones monetarias; negocios inmobiliarios.', '["finanzas", "seguros", "banca", "inmobiliaria", "inversiones"]'),
('37', 'Construcción y reparaciones', 'Servicios de construcción; servicios de reparación; servicios de instalación.', '["construcción", "reparación", "instalación", "mantenimiento", "remodelación"]'),
('38', 'Telecomunicaciones', 'Telecomunicaciones.', '["telecomunicaciones", "internet", "telefonía", "comunicaciones", "redes"]'),
('39', 'Transporte y almacenamiento', 'Transporte; embalaje y almacenamiento de mercancías; organización de viajes.', '["transporte", "logística", "almacenamiento", "viajes", "mensajería"]'),
('40', 'Tratamiento de materiales', 'Tratamiento de materiales.', '["manufactura", "procesamiento", "tratamiento", "reciclaje", "impresión"]'),
('41', 'Educación y entretenimiento', 'Educación; formación; servicios de entretenimiento; actividades deportivas y culturales.', '["educación", "capacitación", "entretenimiento", "deportes", "cultura"]'),
('42', 'Servicios científicos y tecnológicos', 'Servicios científicos y tecnológicos, así como servicios de investigación y diseño en estos ámbitos; servicios de análisis e investigación industrial; diseño y desarrollo de equipos informáticos y de software.', '["tecnología", "software", "investigación", "diseño", "informática"]'),
('43', 'Servicios de restauración y hospedaje', 'Servicios de restauración (alimentación); hospedaje temporal.', '["restaurantes", "hoteles", "hospedaje", "catering", "alimentación"]'),
('44', 'Servicios médicos y de belleza', 'Servicios médicos; servicios veterinarios; tratamientos de higiene y de belleza para personas o animales; servicios de agricultura, horticultura y silvicultura.', '["médicos", "salud", "belleza", "veterinaria", "spa"]'),
('45', 'Servicios jurídicos y de seguridad', 'Servicios jurídicos; servicios de seguridad para la protección de bienes y personas; servicios personales y sociales prestados por terceros para satisfacer necesidades individuales.', '["legal", "abogados", "seguridad", "notaría", "servicios personales"]'),
('99', 'OTRA CATEGORÍA', 'Categoría personalizada para actividades no clasificadas en las clases 1-45 de NIZA. El usuario deberá especificar la categoría.', '["otra", "otro", "personalizada", "custom", "especial"]');

-- =============================================
-- UPSELLING INVITATIONS ENHANCEMENT
-- =============================================

-- Add message content columns to track WhatsApp and email messages
ALTER TABLE `upselling_invitations` 
ADD COLUMN `whatsapp_message` TEXT NULL 
COMMENT 'WhatsApp message content sent'
AFTER `payment_link_url`;

ALTER TABLE `upselling_invitations` 
ADD COLUMN `email_subject` VARCHAR(255) NULL 
COMMENT 'Email subject if sent via email'
AFTER `whatsapp_message`;

ALTER TABLE `upselling_invitations` 
ADD COLUMN `email_message` TEXT NULL 
COMMENT 'Email message content sent'
AFTER `email_subject`;

ALTER TABLE `upselling_invitations` 
ADD COLUMN `contact_whatsapp` VARCHAR(20) NULL 
COMMENT 'WhatsApp number message was sent to'
AFTER `email_message`;

ALTER TABLE `upselling_invitations` 
ADD COLUMN `contact_email` VARCHAR(255) NULL 
COMMENT 'Email address message was sent to'
AFTER `contact_whatsapp`;

-- =============================================
-- NIZA CUSTOM CATEGORY SUPPORT
-- =============================================

-- Add column for custom NIZA category when "OTRA CATEGORÍA" is selected
ALTER TABLE `contacts` 
ADD COLUMN `niza_custom_category` VARCHAR(255) NULL 
COMMENT 'Custom NIZA category description when class 99 (OTRA) is selected'
AFTER `niza_classification`;

-- =============================================
-- AUDIT LOG FOR VERSION UPDATE
-- =============================================

INSERT INTO `audit_log` (`user_id`, `action`, `table_name`, `record_id`, `new_values`, `ip_address`, `created_at`)
VALUES (
    NULL,
    'schema_update',
    NULL,
    NULL,
    '{"version":"2.6.0","changes":["Renamed Expediente Digital Único to Expediente Digital Afiliado (EDA)","Added person_type (fisica/moral) based on RFC length","Complete NIZA 45-class classification with OTRA CATEGORÍA option","Enhanced upselling invitations with WhatsApp and email messaging","Added message content tracking for upselling invitations"]}',
    '127.0.0.1',
    NOW()
);

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================
-- DEPLOYMENT NOTES
-- =============================================
-- After deploying this update:
-- 
-- 1. Expediente Digital Afiliado (EDA):
--    - Renamed from "Expediente Digital Único"
--    - Same functionality, new naming convention
-- 
-- 2. Person Type (Persona Física vs Persona Moral):
--    - contacts.person_type: 'fisica' (RFC 13 chars) or 'moral' (RFC 12 chars)
--    - Persona Física: Has owner (dueño) - use owner_name field
--    - Persona Moral: Has legal representative - use legal_representative field
--    - Forms should show appropriate fields based on RFC length
-- 
-- 3. NIZA Classification:
--    - Complete 45 official classes from Mexican NIZA
--    - Class 99: OTRA CATEGORÍA for custom entries
--    - When class 99 is selected, niza_custom_category stores the custom description
-- 
-- 4. Upselling Invitations Enhancement:
--    - Now tracks WhatsApp message content (whatsapp_message)
--    - Now tracks email subject and content (email_subject, email_message)
--    - Records contact info used (contact_whatsapp, contact_email)
--    - Documents date/time with invitation_date timestamp

-- =============================================
-- VERIFICATION QUERIES
-- =============================================
-- Check person type distribution:
-- SELECT person_type, COUNT(*) FROM contacts WHERE contact_type = 'afiliado' GROUP BY person_type;
-- 
-- Check NIZA classifications:
-- SELECT class_number, name FROM niza_classifications ORDER BY CAST(class_number AS UNSIGNED);
-- 
-- Check contacts with custom NIZA:
-- SELECT id, business_name, niza_classification, niza_custom_category 
-- FROM contacts WHERE niza_classification = '99';

-- =============================================
-- ROLLBACK SCRIPT (if needed)
-- =============================================
-- ALTER TABLE contacts DROP COLUMN person_type;
-- ALTER TABLE contacts DROP COLUMN niza_custom_category;
-- ALTER TABLE upselling_invitations DROP COLUMN whatsapp_message;
-- ALTER TABLE upselling_invitations DROP COLUMN email_subject;
-- ALTER TABLE upselling_invitations DROP COLUMN email_message;
-- ALTER TABLE upselling_invitations DROP COLUMN contact_whatsapp;
-- ALTER TABLE upselling_invitations DROP COLUMN contact_email;
-- DELETE FROM audit_log WHERE new_values LIKE '%"version":"2.6.0"%';
