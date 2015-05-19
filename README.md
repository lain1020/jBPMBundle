# jBPMBundle

A symfony2 bundle to communicate to JBPM 6 API.


## Configuration example

You can configure default Client parameters and Task parameters
Examples:

    ```
    // ezpublish/config/task.conf.yml
    parameters:
        jbpm.client.config:
            username: xxxx
            password: xxxxx
            baseurl: http://localhost:8180/jbpm-console/rest/

        jbpm.task.config:
            cms(project name):
               publish(process name):
                   publishevent1(task name): xrow\jBPMBundle\Tests\publishevent1 (set path of the external function )
                   publishevent2(task name): wuv\aboshopbundle\tasks\publishevent2
               order(process name):
                   oderevent1(task name): xrow\jBPMBundle\Tests\publishevent1
                   oderevent2(task name): xrow\jBPMBundle\Tests\publishevent1
            ecommerce(project name):
               order(process name):
                   oderevent1(task name): wuv\aboshopbundle\tasks\oderevent1
    ```

Import `task.conf.yml` in `ezpublish/config/ezpublish.yml` by adding:
    
    ```
    imports:
         - { resource: task.conf.yml }
    ```