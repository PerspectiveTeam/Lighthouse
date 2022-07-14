## Perspective Lighthouse

### Install this package from BitBucket
To install this package from BitBucket, use the following steps:

1. Go to Magento 2 root directory.
   1. Choose your version and use it in order that they written
      1.  For Magento 2 use following command enter the following commands:  
       ```
       composer config repositories.perspective/module-lighthouse vcs https://bitbucket.org/monteshot/perspective_lighthouse.git
       ```  
       ```
       composer require perspective/module-lighthouse:"dev-master" -vvv
       ```  
      2. Install Node. Tested on v16.15.1
         1. Or you can try to use built-in node and dependencies 
   2. If insllation in app/code then install required dependencies:
      1. ```"dzava/lighthouse"```
      2. ```"hyva-themes/module-magento2-admin"```
2. Wait while all dependencies are update. 
3. Make an ordinary setup for the module
