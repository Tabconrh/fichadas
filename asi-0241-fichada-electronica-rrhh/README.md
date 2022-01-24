#_______________________________
#APP DOTACIONES
#_________________________________

#Pasos de implementación:

#1.- Instalación de git, apache, php y módulos:

yum install git-all httpd php php-gd php-soap php-mbstring php-intl php-pecl-apcu php-json

#2.- Copiado de archivos:
cd ~
git clone https://repositorio-asi.buenosaires.gob.ar/usuarioqa/asi-0241-fichada-electronica-rrhh.git
cp -R ~/asi-0241-fichada-electronica-rrhh/source/* /var/www/html/

#3.- Reinicio del apache:

service httpd restart
