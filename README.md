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
   4. Install Chromium  
      ```
      apt-get install chromium
      ```  
      or  
      ```
      yum install chromium
      ```
      or just run this command if Ubuntu
      ```  
      sudo apt-get install wget -y; wget https://dl.google.com/linux/direct/google-chrome-stable_current_amd64.deb ; sudo  dpkg -i ./google-chrome-stable_current_amd64.deb ; sudo apt-get install -f -y
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
