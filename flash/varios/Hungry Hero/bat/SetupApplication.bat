:user_configuration

:: About AIR application packaging
:: http://livedocs.adobe.com/flex/3/html/help.html?content=CommandLineTools_5.html#1035959
:: http://livedocs.adobe.com/flex/3/html/distributing_apps_4.html#1037515

:: NOTICE: all paths are relative to project root

:: Application descriptor
set APP_XML=application.xml

:: Files to package
set FILE_OR_DIR=-C bin .
set FILE_OR_DIR=-C ..\assets . %FILE_OR_DIR%

:: Your application ID (must match <id> of Application descriptor)
set APP_ID=com.gamua.starling.demo

:: Output packages
set DIST_PATH=bin
set DIST_NAME=StarlingDemo

:: Debugging using a custom IP
set DEBUG_IP=

set AND_CERT_NAME="android"
set AND_CERT_PASS=fd
set AND_CERT_FILE=cert\android.p12
set AND_ICONS=..\assets_system

set AND_SIGNING_OPTIONS=-storetype pkcs12 -keystore "%AND_CERT_FILE%" -storepass %AND_CERT_PASS%

:validation
%SystemRoot%\System32\find /C "<id>%APP_ID%</id>" "%APP_XML%" > NUL
if errorlevel 1 goto badid
goto end

:badid
echo.
echo ERROR: 
echo   Application ID in 'bat\SetupApplication.bat' (APP_ID) 
echo   does NOT match Application descriptor '%APP_XML%' (id)
echo.

:end