
if exist %~dp0epx__250712_01_std_shell__pax__klude_org__github\.start.bat (
    call %~dp0epx__250712_01_std_shell__pax__klude_org__github\.start.bat %*
) else if exist %~dp0.local-plugins\epx__250712_01_std_shell__pax__klude_org__github\.start.bat (
    call %~dp0.local-plugins\epx__250712_01_std_shell__pax__klude_org__github\.start.bat %*
) else (
    curl --globoff -o "epx__250712_01_std_shell__pax__klude_org__github/.start.bat" "https://raw.githubusercontent.com/klude-org/epx-php/main/plugins/epx__250712_01_std_shell__pax__klude_org__github/.start.bat"
)




