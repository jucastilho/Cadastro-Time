<?php
// includes/funcoes.php

function sanitizar($valor) {
    return htmlspecialchars(strip_tags(trim($valor)), ENT_QUOTES, 'UTF-8');
}
