#!/bin/bash
# Script para descargar e instalar FPDF

# Crear directorio libs si no existe
mkdir -p libs

# Descargar FPDF
cd libs
wget http://www.fpdf.org/en/download/fpdf185.zip

# Descomprimir
unzip fpdf185.zip

# Limpiar
rm fpdf185.zip

echo "FPDF instalado correctamente en libs/fpdf185/"
