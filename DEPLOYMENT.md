**Useful Docker Reference**
    
    eval $(docker-machine env -u)
    docker build -t CONTAINERNAME .
    docker run -v "`pwd`":/app -p 80:80 -e ENABLE_PANEL=true --name HOST_localhost CONTAINERNAME

**get remote shell**
    
    docker exec -it HOST-stg /bin/sh   
    docker exec -it HOST-prod /bin/sh

**sweet quick deploy for stg**

    eval $(DOCKER-MACHINE-NAME)
    docker build -t CONTAINERNAME .
    docker-compose up -d --force-recreate


**sweet quick deploy for prod**

    eval $(DOCKER-MACHINE-NAME)
    docker build -t CONTAINERNAME .
    docker-compose -f docker-compose.prod.yml up -d --force-recreate

**periodic cleanup commands**
https://docs.docker.com/engine/reference/commandline/image_prune/
    
    docker image prune -a

**periodic letsencrypt commands**
    
    docker exec DOCKER-MACHINE-NAME /app/force_renew