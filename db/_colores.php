<?php
//Defino la variable 
if (strpos($persona, 'ariano') !== false): $color = "primary"; 
elseif (strpos($persona, 'antiago') !== false): $color = "info";
elseif (strpos($persona, 'rancisco') !== false): $color = "success";
elseif (strpos($persona, 'aquel') !== false): $color = "danger";
elseif (strpos($persona, 'nrique') !== false): $color = "warning";
elseif (strpos($persona, 'Leo') !== false): $color = "normal";
elseif (strpos($persona, 'Hernan') !== false): $color = "normal";
elseif (strpos($persona, 'anuel') !== false): $color = "normal";
elseif (strpos($persona, 'ictor') !== false): $color = "default";
elseif (strpos($persona, 'ugenio') !== false): $color = "primary";
endif; ?>

