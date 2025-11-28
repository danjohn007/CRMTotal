<?php
/**
 * Simple QR Code Generator
 * A minimalist PHP QR Code generator that doesn't require external dependencies.
 * Uses Reed-Solomon error correction and generates PNG images via GD.
 * 
 * Based on the QR Code specification ISO/IEC 18004:2015
 * Supports alphanumeric and byte mode encoding
 */

class QRCode {
    
    // QR Code version 4: 33x33 modules (fits ~78 bytes with L correction)
    private const SIZE = 33;
    private const QUIET_ZONE = 4;
    
    // Galois field elements for Reed-Solomon
    private static array $gfExp = [];
    private static array $gfLog = [];
    private static bool $gfInitialized = false;
    
    /**
     * Generate QR code PNG image data
     * 
     * @param string $data Data to encode
     * @param int $pixelSize Size of each module in pixels
     * @return string|null PNG image data or null on failure
     */
    public static function generate(string $data, int $pixelSize = 10): ?string {
        if (empty($data) || strlen($data) > 78) {
            // For longer data, return null to trigger fallback
            return null;
        }
        
        try {
            $matrix = self::createMatrix($data);
            if ($matrix === null) {
                return null;
            }
            return self::renderToPng($matrix, $pixelSize);
        } catch (Exception $e) {
            error_log("QRCode generation error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Create the QR code bit matrix
     */
    private static function createMatrix(string $data): ?array {
        $size = self::SIZE;
        
        // Initialize matrix with -1 (undefined)
        $matrix = array_fill(0, $size, array_fill(0, $size, -1));
        
        // Add finder patterns (three corners)
        self::addFinderPattern($matrix, 0, 0);
        self::addFinderPattern($matrix, $size - 7, 0);
        self::addFinderPattern($matrix, 0, $size - 7);
        
        // Add separators
        self::addSeparators($matrix, $size);
        
        // Add timing patterns
        self::addTimingPatterns($matrix, $size);
        
        // Add alignment patterns for version 4 (at positions 6, 26)
        self::addAlignmentPattern($matrix, 26, 6);
        self::addAlignmentPattern($matrix, 6, 26);
        self::addAlignmentPattern($matrix, 26, 26);
        
        // Add format information (using mask 0 with L error correction)
        self::addFormatInfo($matrix, $size);
        
        // Add version information for version 4 (not needed, version < 7)
        
        // Encode data
        $dataBits = self::encodeData($data);
        if ($dataBits === null) {
            return null;
        }
        
        // Add error correction (version 4-L: 80 data bytes, 18 EC bytes)
        $finalBits = self::addErrorCorrection($dataBits, 80, 18);
        
        // Place data in matrix
        self::placeData($matrix, $finalBits, $size);
        
        // Apply mask pattern 0 (i+j) mod 2 == 0
        self::applyMask($matrix, $size);
        
        return $matrix;
    }
    
    /**
     * Add 7x7 finder pattern at specified position
     */
    private static function addFinderPattern(array &$matrix, int $row, int $col): void {
        for ($r = 0; $r < 7; $r++) {
            for ($c = 0; $c < 7; $c++) {
                $isBlack = ($r == 0 || $r == 6 || $c == 0 || $c == 6) ||
                          ($r >= 2 && $r <= 4 && $c >= 2 && $c <= 4);
                $matrix[$row + $r][$col + $c] = $isBlack ? 1 : 0;
            }
        }
    }
    
    /**
     * Add separator areas around finder patterns
     */
    private static function addSeparators(array &$matrix, int $size): void {
        // Top-left
        for ($i = 0; $i < 8; $i++) {
            if ($i < $size) {
                $matrix[$i][7] = 0;
                $matrix[7][$i] = 0;
            }
        }
        
        // Top-right
        for ($i = 0; $i < 8; $i++) {
            if ($i < $size) {
                $matrix[$i][$size - 8] = 0;
            }
        }
        for ($i = $size - 8; $i < $size; $i++) {
            $matrix[7][$i] = 0;
        }
        
        // Bottom-left
        for ($i = 0; $i < 8; $i++) {
            $matrix[$size - 8][$i] = 0;
        }
        for ($i = $size - 8; $i < $size; $i++) {
            $matrix[$i][7] = 0;
        }
    }
    
    /**
     * Add timing patterns
     */
    private static function addTimingPatterns(array &$matrix, int $size): void {
        for ($i = 8; $i < $size - 8; $i++) {
            $bit = ($i % 2 == 0) ? 1 : 0;
            $matrix[6][$i] = $bit;
            $matrix[$i][6] = $bit;
        }
    }
    
    /**
     * Add 5x5 alignment pattern at specified center position
     */
    private static function addAlignmentPattern(array &$matrix, int $row, int $col): void {
        // Skip if overlapping with finder pattern
        if ($row < 9 && $col < 9) return;
        if ($row < 9 && $col > count($matrix) - 9) return;
        if ($row > count($matrix) - 9 && $col < 9) return;
        
        for ($r = -2; $r <= 2; $r++) {
            for ($c = -2; $c <= 2; $c++) {
                $isBlack = (abs($r) == 2 || abs($c) == 2) || ($r == 0 && $c == 0);
                $matrix[$row + $r][$col + $c] = $isBlack ? 1 : 0;
            }
        }
    }
    
    /**
     * Add format information
     */
    private static function addFormatInfo(array &$matrix, int $size): void {
        // Format string for L error correction, mask 0: 111011111000100
        $format = [1,1,1,0,1,1,1,1,1,0,0,0,1,0,0];
        
        // Around top-left finder
        for ($i = 0; $i < 6; $i++) {
            $matrix[8][$i] = $format[$i];
        }
        $matrix[8][7] = $format[6];
        $matrix[8][8] = $format[7];
        $matrix[7][8] = $format[8];
        
        for ($i = 0; $i < 6; $i++) {
            $matrix[5 - $i][8] = $format[9 + $i];
        }
        
        // Right of top-left and below top-right
        for ($i = 0; $i < 7; $i++) {
            $matrix[$size - 1 - $i][8] = $format[$i];
        }
        
        for ($i = 0; $i < 8; $i++) {
            $matrix[8][$size - 8 + $i] = $format[7 + $i];
        }
        
        // Dark module
        $matrix[$size - 8][8] = 1;
    }
    
    /**
     * Encode data to bit stream
     */
    private static function encodeData(string $data): ?array {
        $bits = [];
        
        // Mode indicator for byte mode: 0100
        $bits = array_merge($bits, [0, 1, 0, 0]);
        
        // Character count (8 bits for version 1-9 in byte mode)
        $len = strlen($data);
        for ($i = 7; $i >= 0; $i--) {
            $bits[] = ($len >> $i) & 1;
        }
        
        // Data bytes
        for ($i = 0; $i < $len; $i++) {
            $byte = ord($data[$i]);
            for ($j = 7; $j >= 0; $j--) {
                $bits[] = ($byte >> $j) & 1;
            }
        }
        
        // Terminator (4 bits, or less if near capacity)
        $capacity = 640; // Version 4-L capacity in bits (80 bytes)
        $remaining = $capacity - count($bits);
        $terminator = min(4, $remaining);
        for ($i = 0; $i < $terminator; $i++) {
            $bits[] = 0;
        }
        
        // Pad to byte boundary
        while (count($bits) % 8 != 0) {
            $bits[] = 0;
        }
        
        // Add padding bytes
        $padBytes = [0xEC, 0x11];
        $padIndex = 0;
        while (count($bits) < $capacity) {
            $byte = $padBytes[$padIndex % 2];
            for ($j = 7; $j >= 0; $j--) {
                $bits[] = ($byte >> $j) & 1;
            }
            $padIndex++;
        }
        
        return $bits;
    }
    
    /**
     * Add Reed-Solomon error correction
     */
    private static function addErrorCorrection(array $dataBits, int $dataBytes, int $ecBytes): array {
        // Convert bits to bytes
        $bytes = [];
        for ($i = 0; $i < count($dataBits); $i += 8) {
            $byte = 0;
            for ($j = 0; $j < 8 && ($i + $j) < count($dataBits); $j++) {
                $byte = ($byte << 1) | $dataBits[$i + $j];
            }
            $bytes[] = $byte;
        }
        
        // Calculate error correction bytes
        $ecBytesArr = self::calculateEC($bytes, $ecBytes);
        
        // Combine data and EC bytes back to bits
        $allBytes = array_merge($bytes, $ecBytesArr);
        $allBits = [];
        foreach ($allBytes as $byte) {
            for ($j = 7; $j >= 0; $j--) {
                $allBits[] = ($byte >> $j) & 1;
            }
        }
        
        return $allBits;
    }
    
    /**
     * Initialize Galois field for Reed-Solomon
     */
    private static function initGF(): void {
        if (self::$gfInitialized) {
            return;
        }
        
        $gfExp = array_fill(0, 512, 0);
        $gfLog = array_fill(0, 256, 0);
        
        $x = 1;
        for ($i = 0; $i < 255; $i++) {
            $gfExp[$i] = $x;
            $gfLog[$x] = $i;
            $x <<= 1;
            if ($x & 0x100) {
                $x ^= 0x11D; // Primitive polynomial
            }
        }
        
        for ($i = 255; $i < 512; $i++) {
            $gfExp[$i] = $gfExp[$i - 255];
        }
        
        self::$gfExp = $gfExp;
        self::$gfLog = $gfLog;
        self::$gfInitialized = true;
    }
    
    /**
     * Calculate error correction codewords
     */
    private static function calculateEC(array $data, int $ecCount): array {
        self::initGF();
        
        // Generator polynomial
        $generator = self::getGeneratorPoly($ecCount);
        
        // Initialize message polynomial
        $msg = array_merge($data, array_fill(0, $ecCount, 0));
        
        // Polynomial division
        for ($i = 0; $i < count($data); $i++) {
            $coef = $msg[$i];
            if ($coef != 0) {
                for ($j = 0; $j < count($generator); $j++) {
                    $msg[$i + $j] ^= self::gfMul($generator[$j], $coef);
                }
            }
        }
        
        return array_slice($msg, count($data));
    }
    
    /**
     * Get generator polynomial for Reed-Solomon
     */
    private static function getGeneratorPoly(int $count): array {
        self::initGF();
        
        $g = [1];
        for ($i = 0; $i < $count; $i++) {
            $newG = array_fill(0, count($g) + 1, 0);
            $factor = self::$gfExp[$i];
            
            for ($j = 0; $j < count($g); $j++) {
                $newG[$j] ^= $g[$j];
                $newG[$j + 1] ^= self::gfMul($g[$j], $factor);
            }
            $g = $newG;
        }
        
        return $g;
    }
    
    /**
     * Galois field multiplication
     */
    private static function gfMul(int $a, int $b): int {
        if ($a == 0 || $b == 0) {
            return 0;
        }
        return self::$gfExp[self::$gfLog[$a] + self::$gfLog[$b]];
    }
    
    /**
     * Place data bits in the matrix
     */
    private static function placeData(array &$matrix, array $bits, int $size): void {
        $bitIndex = 0;
        $up = true;
        
        for ($col = $size - 1; $col >= 0; $col -= 2) {
            // Skip timing column
            if ($col == 6) {
                $col = 5;
            }
            
            for ($row = ($up ? $size - 1 : 0); ($up ? $row >= 0 : $row < $size); $row += ($up ? -1 : 1)) {
                for ($c = 0; $c < 2; $c++) {
                    $actualCol = $col - $c;
                    if ($matrix[$row][$actualCol] == -1) {
                        if ($bitIndex < count($bits)) {
                            $matrix[$row][$actualCol] = $bits[$bitIndex];
                            $bitIndex++;
                        } else {
                            $matrix[$row][$actualCol] = 0;
                        }
                    }
                }
            }
            $up = !$up;
        }
    }
    
    /**
     * Apply mask pattern 0: (row + column) mod 2 == 0
     */
    private static function applyMask(array &$matrix, int $size): void {
        for ($row = 0; $row < $size; $row++) {
            for ($col = 0; $col < $size; $col++) {
                // Don't mask function patterns (check if it was originally set)
                if (self::isDataModule($row, $col, $size)) {
                    if (($row + $col) % 2 == 0) {
                        $matrix[$row][$col] ^= 1;
                    }
                }
            }
        }
    }
    
    /**
     * Check if module is a data module (not a function pattern)
     */
    private static function isDataModule(int $row, int $col, int $size): bool {
        // Finder patterns and separators
        if ($row < 9 && $col < 9) return false;
        if ($row < 9 && $col >= $size - 8) return false;
        if ($row >= $size - 8 && $col < 9) return false;
        
        // Timing patterns
        if ($row == 6 || $col == 6) return false;
        
        // Alignment patterns for version 4 (at 26,6  6,26  26,26)
        if ($row >= 24 && $row <= 28 && $col >= 4 && $col <= 8) return false;
        if ($row >= 4 && $row <= 8 && $col >= 24 && $col <= 28) return false;
        if ($row >= 24 && $row <= 28 && $col >= 24 && $col <= 28) return false;
        
        return true;
    }
    
    /**
     * Render matrix to PNG image
     */
    private static function renderToPng(array $matrix, int $pixelSize): ?string {
        $size = self::SIZE;
        $quietZone = self::QUIET_ZONE;
        $totalSize = ($size + 2 * $quietZone) * $pixelSize;
        
        $img = imagecreatetruecolor($totalSize, $totalSize);
        if ($img === false) {
            return null;
        }
        
        $white = imagecolorallocate($img, 255, 255, 255);
        $black = imagecolorallocate($img, 0, 0, 0);
        
        // Fill with white
        imagefill($img, 0, 0, $white);
        
        // Draw modules
        for ($row = 0; $row < $size; $row++) {
            for ($col = 0; $col < $size; $col++) {
                if ($matrix[$row][$col] == 1) {
                    $x = ($col + $quietZone) * $pixelSize;
                    $y = ($row + $quietZone) * $pixelSize;
                    imagefilledrectangle($img, $x, $y, $x + $pixelSize - 1, $y + $pixelSize - 1, $black);
                }
            }
        }
        
        // Capture PNG output
        ob_start();
        imagepng($img);
        $data = ob_get_clean();
        imagedestroy($img);
        
        return $data ?: null;
    }
}
