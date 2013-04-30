@echo off
set PAUSE_ERRORS=1
call bat\SetupSDK.bat
call bat\SetupApplication.bat

:menu
echo.
echo Package for target
echo.
echo Android:
echo.
echo  [1] normal       (apk)
echo  [2] debug        (apk-debug)
echo  [3] captive      (apk-captive-runtime)
echo.

:choice
set /P C=[Choice]: 
echo.

set PLATFORM=android
set OPTIONS=


if "%C%"=="1" set TARGET=
if "%C%"=="2" set TARGET=-debug
if "%C%"=="2" set OPTIONS=-connect %DEBUG_IP%
if "%C%"=="3" set TARGET=-captive-runtime



call bat\Packager.bat

if "%PLATFORM%"=="android" goto android-package


:android-package
adb devices
echo.
echo Installing %OUTPUT% on the device...
echo.
adb -d install -r "%OUTPUT%"
if errorlevel 1 goto installfail
goto end

:installfail
echo.
echo Installing the app on the device failed

:end
pause
