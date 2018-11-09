[![Logo](https://XXXXX.com/logo.png)](https://example.com.com)

{description}

When using this as a new project:
- update all vars in .env file
- update {IMAGENAME} in the README for both the build and run commands
- update ENV variables set at the end of the dockerfile
- 



## Setting up

### Requirements
- Docker

### Clone the Repo w/submodules

    git clone --recursive {repository url}

### Build the app

Make sure you are working on your local docker machine and not a remote
```
eval $(docker-machine env -u)
```

Use value for IMAGENAME from .env file for build command
```
docker build -t {IMAGENAME} .
```

This builds the docker image with everything needed to host this app.


# run local environment
```
docker run -v "`pwd`":/app -p 80:80 -e ENABLE_PANEL=true --name {CONTAINERNAME}_localhost {IMAGENAME}
```

This runs your new container for the first time with your local project's folder mounted and overlaying the container's folder that it is serving.  This way any local changes to your folder are instantly reflected in the server environment.

Note that this will also run the server on port 80 and give the container you've just run a name of localhost and will cache it in the docker library.  From now on you won't need to use the ``docker run`` command to start this container.  Instead you will use:

``docker start {container}`` and ``docker stop {container}`` to start and stop it.

To see available cached docker containers you can run ``docker ps`` to see the currently running containers or ``docker ps -a`` to see all of the containers... even the ones that are not running.

Whenever the Dockerfile changes you will need to re-run the ``docker build`` command.

You can do most of this nicely and visually now with [Kitematic](https://kitematic.com/)



## Notes
[Docker/Deployment Guide](DEPLOYMENT.md)





