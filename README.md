# jBPMBundle

A symfony2 bundle to communicate to JBPM 6 API.


## Configuration example

You can configure default client parameters and task parameters
Examples:

    ```
    // ezpublish/config/jbpmconf.yml
    parameters:
        jbpm.client.config:
            username: xxxx
            password: xxxxx
            baseurl: http://localhost/jbpm-console/rest/
            defaults_verify: true|false (true is default)

        jbpm.task.config:
            cms(project name):
               publish(process name):
                   publishevent1(task name): xrow\jBPMBundle\Tests\publishevent1 (set path of the external function )
                   publishevent2(task name): customer\shopbundle\tasks\publishevent2
               order(process name):
                   oderevent1(task name): xrow\jBPMBundle\Tests\publishevent1
                   oderevent2(task name): xrow\jBPMBundle\Tests\publishevent1
            ecommerce(project name):
               order(process name):
                   oderevent1(task name): customer\shopbundle\tasks\oderevent1
    ```

Import `jbpmconf.yml` in `ezpublish/config/ezpublish.yml` by adding:
    
    ```
    imports:
         - { resource: jbpmconf.yml }
    ```