<?php

//***  OpenSUSE 11.0 default settings

//* Main
$conf['language'] = 'en';
$conf['distname'] = 'opensuse110';
$conf['hostname'] = 'server1.example.com'; // Full hostname
$conf['ispconfig_install_dir'] = '/usr/local/ispconfig';
$conf['ispconfig_config_dir'] = '/usr/local/ispconfig';
$conf['ispconfig_log_priority'] = 2;  // 0 = Debug, 1 = Warning, 2 = Error
$conf['server_id'] = 1;
$conf['init_scripts'] = '/etc/init.d';
$conf['runlevel'] = '/etc';
$conf['shells'] = '/etc/shells';
$conf['cron_tab'] = '/var/spool/cron/tabs/root';
$conf['pam'] = '/etc/pam.d';

//* MySQL
$conf['mysql']['init_script'] = 'mysql';
$conf['mysql']['host'] = 'localhost';
$conf['mysql']['ip'] = '127.0.0.1';
$conf['mysql']['port'] = '3306';
$conf['mysql']['database'] = 'dbispconfig';
$conf['mysql']['admin_user'] = 'root';
$conf['mysql']['admin_password'] = '';
$conf['mysql']['ispconfig_user'] = 'ispconfig';
$conf['mysql']['ispconfig_password'] = md5 (uniqid (rand()));

//* Apache
$conf['apache']['user'] = 'wwwrun';
$conf['apache']['group'] = 'www';
$conf['apache']['init_script'] = 'apache2';
$conf['apache']['version'] = '2.2';
$conf['apache']['vhost_conf_dir'] = '/etc/apache2/sites-available';
$conf['apache']['vhost_conf_enabled_dir'] = '/etc/apache2/sites-enabled';
$conf['apache']['vhost_port'] = '8080';

//* Postfix
$conf['postfix']['config_dir'] = '/etc/postfix';
$conf['postfix']['init_script'] = 'postfix';
$conf['postfix']['user'] = 'postfix';
$conf['postfix']['group'] = 'postfix';
$conf['postfix']['vmail_userid'] = '5000';
$conf['postfix']['vmail_username'] = 'vmail';
$conf['postfix']['vmail_groupid'] = '5000';
$conf['postfix']['vmail_groupname'] = 'vmail';
$conf['postfix']['vmail_mailbox_base'] = '/home/vmail';

//* Getmail
$conf['getmail']['config_dir'] = '/etc/getmail';
$conf['getmail']['program'] = '/usr/bin/getmail';

//* Courier
$conf['courier']['config_dir'] = '/etc/authlib';
$conf['courier']['courier-authdaemon'] = 'courier-authdaemon';
$conf['courier']['courier-imap'] = 'courier-imap';
$conf['courier']['courier-imap-ssl'] = 'courier-imap-ssl';
$conf['courier']['courier-pop'] = 'courier-pop';
$conf['courier']['courier-pop-ssl'] = 'courier-pop-ssl';

//* SASL
$conf['saslauthd']['config'] = '/etc/default/saslauthd';
$conf['saslauthd']['init_script'] = 'saslauthd';

//* Amavisd
$conf['amavis']['config_dir'] = '/etc/amavis';
$conf['amavis']['init_script'] = 'amavis';

//* ClamAV
$conf['clamav']['init_script'] = 'clamd';

//* Pureftpd
$conf['pureftpd']['config_dir'] = '/etc/pure-ftpd';
$conf['pureftpd']['init_script'] = 'pure-ftpd';

//* MyDNS
$conf['mydns']['config_dir'] = '/etc';
$conf['mydns']['init_script'] = 'mydns';

//* Jailkit
$conf['jailkit']['config_dir'] = '/etc/jailkit';
$conf['jailkit']['jk_init'] = 'jk_init.ini';
$conf['jailkit']['jk_chrootsh'] = 'jk_chrootsh.ini';

?>