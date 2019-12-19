<?php

foreach ($items as $crop_id => $crops)
{
    $size = sizeof($crops['varieties']);
    $i = 0;
    $variety_options = '<table style="width:100%"><tr>';
    foreach($crops['varieties'] as $variety_id => $variety_name){
        $i++;
        $checked = (in_array($variety_id, $compared_varieties))? 'checked':'';
        $variety_options .= '<td>
                                 <div class="checkbox" style="margin:0">
                                      <input type="checkbox" name="variety[]" value="' . $variety_id . '" '.$checked.'><span>' . $variety_name . '</span>
                                  </div>
                             </td>';
        $variety_options .=  (($i % 2 == 0) && ($i != $size))? '</tr><tr>':'';
    }
    $variety_options.='</tr></table>';

     echo '<div class="row show-grid">
               <div class="col-xs-3">
                   <label><u style="font-size:0.9em">'. $crops['crop_name'] .'</u> :</label>
               </div>
               <div class="col-xs-9" style="margin-top:15px">
                    '. $variety_options .'
               </div>
           </div>';
}
