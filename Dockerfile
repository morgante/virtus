FROM 		dockerfile/python
MAINTAINER	Morgante Pell <me@morgante.net>

# Update
RUN  		apt-get update

# Buildable
RUN 		apt-get install -y python-software-properties

# Node
WORKDIR 	/tmp
RUN 		wget -O node.tar.gz http://nodejs.org/dist/v0.10.28/node-v0.10.28.tar.gz
RUN 		tar xvzf node.tar.gz
RUN 		rm node.tar.gz
RUN 		mv node-* node
WORKDIR 	/tmp/node
RUN 		./configure
RUN 		CXX="g++ -Wno-unused-local-typedefs" make
RUN 		CXX="g++ -Wno-unused-local-typedefs" make install

## Supervisor
RUN			echo "deb http://archive.ubuntu.com/ubuntu precise main universe" > /etc/apt/sources.list
RUN			apt-get update
RUN			apt-get upgrade -y --force-yes
RUN			apt-get install -y --force-yes supervisor
RUN			mkdir -p /var/log/supervisor
ADD 		./ops/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Install global deps
RUN 		npm install -g gulp

# Start app dir
RUN 		mkdir /app

# Enable execution
ADD 		./ops/start /app/start
RUN			chmod o+x /app/start

# Install app dependencies
ADD 		./package.json /app/package.json
RUN			cd /app; npm install -d

# Load app dependencies
ADD 		. /app/src
RUN 		ln -s /app/src /src
RUN 		cd /app/src

EXPOSE  	3000

WORKDIR 	/app/src
CMD 		["../start"]