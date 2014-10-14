#
# Virtual Host Example for Ramp/Smart
# 
# In the example below, the DocumentRoot for the Ramp or Smart
# environment should be the appropriate ".../ramp/public" directory under
# either the server's overall DocumentRoot directory
# (/var/www in the example below) or a developer's or adminstrator's
# personal DocumentRoot directory (e.g., /home/username/www).  The
# ErrorLog and CustomLog can be in system space, as in the example below,
# or in Ramp's space if you create a directory such as ".../ramp/log".
#
# You can name your server whatever seems appropriate for your context;
# the example below illustrates running a Ramp application named "adminDB".
# The APPLICATION_ENV variable for the server should match the name
# of a section in your application/configs/application.ini file.  For
# example, a rampdemo APPLICATION_ENV for a Ramp Demo server would match
# the rampdemo section in the ramp_demo.ini file in the application/configs
# directory.  If you do not set an APPLICATION_ENV variable, its value
# will default to 'rampdemo'.
#

<VirtualHost *:80>
      ServerName mycompany.com
      ServerAlias adminDB.mycompany.com
      DocumentRoot /var/www/ramp/public
      ErrorLog /var/log/ramp/error.log
      CustomLog /var/log/ramp/access.log combined
      SetEnv APPLICATION_ENV production
      <Directory "/var/www/ramp/public">
          Options MultiViews SymlinksIfOwnerMatch
          AllowOverride All
          Order allow,deny
          Allow from all
      </Directory>
 </VirtualHost>

