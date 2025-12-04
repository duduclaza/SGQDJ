<?php
/**
 * SGQ OTI DJ - Fallback Index
 * Este arquivo redireciona para public/index.php
 */

// Se o DocumentRoot não aponta para /public, este arquivo serve como fallback
require_once __DIR__ . '/public/index.php';
