<?php
require_once('connessione.php');
function renderStatBar($currentVal, $upgradeVal = 0) {
    $totalFilled = $currentVal + $upgradeVal;
    
    $potentialMax = $currentVal + MAX_UPGRADE_LVL; 

    echo '<div class="segmentBar">';
    
    for ($i = 1; $i <= $potentialMax; $i++) {
        $class = '';
        
        if ($i <= $currentVal) {
            $class = 'filled';
        } elseif ($i <= $totalFilled) {
            $class = 'filled upgrade';
        }
        
        echo '<div class="segment ' . $class . '"></div>';
    }
    
    echo '</div>';
}
?>