@echo off
REM Backup db_workshop dengan mysqldump bawaan XAMPP (ubah path jika berbeda)
set MYSQLDUMP=C:\xampp\mysql\bin\mysqldump.exe
set TGL=%date:~-4%-%date:~3,2%-%date:~0,2%
set JAM=%time:~0,2%%time:~3,2%
set JAM=%JAM: =0%
set OUT=%~dp0..\sistem-informasi-akademik\database\backup\db_workshop_%TGL%_%JAM%.sql
"%MYSQLDUMP%" -u root --databases db_workshop > "%OUT%"
echo Backup selesai: %OUT%
pause
