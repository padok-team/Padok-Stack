FROM node:10-alpine

# Create the directory that will contain the app
RUN mkdir -p /opt/app

# Change working dir to the app dir
WORKDIR /opt/app

# Build, run, ... cmds
#  - packages.json      tells yarn what he can do and how
#  - yarn.lock          tells yarn which version of each package needs to be installed
#  - webpack.config.js  webpack's config (the compilation tool)
#  - .babelrc           babel config (also part of the compilation chain)
COPY ./package.json /opt/app/package.json
COPY ./yarn.lock /opt/app/yarn.lock
COPY ./webpack.config.js /opt/app/webpack.config.js
COPY ./.babelrc /opt/app/.babelrc

# External patches
#  - Need a "yarn install" or a "yarn prepare" to be applied
#    Therefore not part of the live reload
COPY ./patches/ /opt/app/patches/

# Sources
#  - Directories that need to be bind mounted in the container to enable live reload
COPY ./src/ /opt/app/src/
COPY ./utils/ /opt/app/utils/

# Run config
#  - Disabled because the config directory is mounted afterward at runtime
#  - However, it could be copied to the Docker image as a default config...
#COPY ./config/ /opt/app/config/

# Install dependancies
RUN yarn install
# Build libs and modules
RUN yarn prod:build

# Command will always be a "yarn <something>"
#  - <something> defaults to "start" which is good for production env
#  - You can also set it to "dev:watch" for dev purposes
ENTRYPOINT [ "yarn" ]
CMD [ "start" ]
