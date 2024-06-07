<?php

if (!function_exists('screenToolbarMenuHtml')) {
  function screenToolbarMenuHtml($screen, $appMenuActions)
  {
    $primaryActions = '';
    $secondActions = '';

    $counter = 0;
    for ($i = 0; $i < count($appMenuActions); $i++) {
      $menuAction = $appMenuActions[$i];
      if ($menuAction['position'] === 'TOOLBAR') {
        $eventFunctionName = (strlen($menuAction['event_name_prefix'] ?? '') > 1 ? $menuAction['event_name_prefix'] : $screen) . $menuAction['event_name'];
        $acions = '<li><div class="SnToolbar-menu-btn ' . $menuAction['class_names'] . ' jsAction" id="' . $eventFunctionName . 'Btn" onclick="' . $eventFunctionName . '(\'' . $screen . '\', \'' . $menuAction['screen_id_controller'] . '\')" title="' . $menuAction['title'] . ' (' . $menuAction['keyboard_key'] . ')">'
          . '<i class="' . $menuAction['icon'] . ' SnMr-2"></i> ' . $menuAction['title'] . ' '
          . "</div></li>";

        if ($counter < 2) {
          $primaryActions .= $acions;
        } else {
          $secondActions .= $acions;
        }
        $counter++;
      }
    }

    // return $primaryActions . '<li class="SnToolbar-mobileToggle"> <i class="fa-solid fa-ellipsis"></i> </li><ul>'.$secondActions.'</ul>';
    return $primaryActions . '<li class="SnToolbar-mobileToggle"><div class="SnToolbar-menu-btn SnToolbar-mobileToggle-icon"><i class="fa-solid fa-ellipsis"></i></div><ul>' . $secondActions . '</ul></li>';
  }
}

if (!function_exists('screenToolbarHtml')) {
  function screenToolbarHtml(string $screen, array $appMenuActions, string $positon = 'TOOLBAR', array $customTitle = [])
  {
    $actionHtml = '';

    foreach ($appMenuActions as $key => $menuAction) {

      if (count($customTitle) > 0) {
        foreach ($customTitle as $row) {
          if ($menuAction['event_name'] == $row['event_name']) {
            $menuAction['title'] = $row['title'] ?? $menuAction['title'];
            $menuAction['icon'] = $row['icon'] ?? $menuAction['icon'];

            if (isset($row['class_names'])) {
              $clasnames = explode(' ', $row['class_names']);
              foreach ($clasnames as $key => $cls) {
                if (str_contains($menuAction['class_names'], $cls)) {
                  $menuAction['class_names'] .= ' ' . $cls;
                }
              }
            }
          }
        }
      }

      $event = !($menuAction['id'] == 25 || $menuAction['id'] == 36) ? "onclick=\"{$screen}{$menuAction['event_name']}('{$screen}','{$menuAction['screen_id_controller']}')\"" : '';

      if ($menuAction['position'] === $positon) {
        $actionHtml .= "<button type=\"{$menuAction['type']}\" class=\"SnBtn {$menuAction['class_names']} SnMr-2 jsAction\" {$event} id=\"{$screen}{$menuAction['event_name']}\" title=\"{$menuAction['description']} ({$menuAction['keyboard_key']})\">"
          . '<i class="' . $menuAction['icon'] . ' SnMr-2"></i>' . $menuAction['title']
          . '</button>';
      }
    }

    return $actionHtml;
  }
}


function durationInSeconds()
{
  return round(microtime(true) - APP_START, 3);
}
