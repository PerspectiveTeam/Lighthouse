## Perspective Lighthouse

### Install this package from BitBucket
To install this package from BitBucket, use the following steps:

1. Go to Magento 2 root directory.  
   1. If you want to install to vendor directory then use the following command:   
   ```
   composer config repositories.perspective/module-lighthouse vcs https://bitbucket.org/monteshot/perspective_lighthouse.git
   ```  
   ```
   composer require perspective/module-lighthouse:"dev-master" -vvv
   ```  
   2. If installation is in app/code then install required dependencies:  
      1. ```"dzava/lighthouse"```  
      2. ```"hyva-themes/module-magento2-admin"```  
   3. Install Node. Tested on v16.15.1
      1. Or you can try to use built-in node and dependencies
   4. Install Chrome Browser 
      by run this command if you have an Ubuntu/Debian (deb/dpkg)  
      ```  
      sudo apt-get install wget -y; wget https://dl.google.com/linux/direct/google-chrome-stable_current_amd64.deb ; sudo apt-get update; sudo  dpkg -i ./google-chrome-stable_current_amd64.deb || sudo apt-get install --fix-broken --fix-missing -y
      ```  
      or just run this command if you have a Fedora/CentOS/OpenSUSE (yum/rpm)  
      ```  
      sudo yum install wget -y; wget https://dl.google.com/linux/direct/google-chrome-stable_current_x86_64.rpm ; sudo yum install -y ./google-chrome-stable_current_x86_64.rpm ;
      ```  
2. Wait for installation or updating the dependencies. 
3. Make an ordinary setup for the module:
   1. ```
      bin/magento mo:e Hyva_Admin Perspective_Lighthouse
      ```  
   2. ```
      bin/magento setup:upgrade
      ```
   3. ```
      bin/magento s:d:c      
      ```  
   4. ```
      bin/magento s:s:d
      ```    
   4. ```
      bin/magento c:f
      ```  
