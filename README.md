# Dockerize BAM stack

This project is about making the BAM stack run in a Dockerized environment.

We will proceed in 2 steps :
 1. Init a BAM stack, using the BAM method and repo (Careful: we won't use all steps of the BAM method)
 2. Add / modify the required files so that the stack will run in Docker

## Init a new BAM stack

You will use the process bellow in order to initiate the BAM stack.

Notes:
- Before using setup.sh, you need to comment the following lines in the script :
   - If you don't want to push the new repo to BAM GitHub repos :
      - 34: hub create -p "bamlab/$NAME"
      - 35: git push -u origin master
      => In this case you also need to add a `true` command so that the script doesn't fail

   - If you don't want travis config :
      - 44: echo "Opening GitHub to configure Travis... Add $API_PROJECT_NAME and $BACKOFFICE_PROJECT_NAME to the list of selected repositories."
      - 45: open "https://github.com/organizations/bamlab/settings/installations/209171"

- When the script asks you where you want your app to be hosted, answer the following :
      - "skip"


1. $ git clone https://github.com/bamlab/create-bam-stack.git <some_path>
2. $ cd <some_path>
3. # Edit the setup.sh script as described above
4. $ ./setup.sh
    -> Answer to the first question with your project's name
    -> Say "yes" to api generation
    -> Say "yes" to backoffice generation
    -> **Say "skip" to hosting configuration**

5. $ grep -r myproject <project_name>-* | cut -d ':' -f 1 | sort | uniq | xargs sed -i 's:myproject:<project_name>:g'
6. $ grep -r MyProject <project_name>-* | cut -d ':' -f 1 | sort | uniq | xargs sed -i 's:MyProject:<beautiful_project_name>:g'

After these few steps you will end up with two newly created Git repo in sub directory:
 - `<project_name>-api`
 - `<project_name>-backoffice`

## Dockerize the BAM stack

Everything in the following sections will only concern the `<project_name>-api` git project created during the previous steps,
and located in the `<project_name>-api` sub-directory.

So from now on we will concider the root dir of our project to be the `<project_name>-api`.

### Docker-related files

Copy the following files to the root dir of your new BAM stack api :
 - Docker-compose.yml -> <root_dir>/docker-compose.yml
 - Dockerfile         -> <root_dir>/Dockerfile
 - dockerignore       -> <root_dir>/.dockerignore

Note: The last one is stored as a regular file in the initial repo, but has to be copied as a hidden file.

These files add the Docker logic to the project so that you can perform a `docker-compose up` commands.

### Config files

Any command may still fail however if you don't set the proper configuration values.

You can do so either by :
 - customizing the config files in `./config` in your new BAM stack api project
 - using the config exemples in `./config` in this repo to customize the config files in the BAM stack api project

Note: the `./config/default.yaml` file available in this repo should be enough to perform a PoC test.

### Firebase config

At this point running the appli will still fail because you need Firebase credentials to make it work.
Which mean you need a Firebase project.
The process of creating a Firebase project and getting its key isn't describe here,
since the 4th step of create-bam-stack README explains it well enough.
When you have it, download the related private key and put it in your config dir under the name `firebase-adminsdk.staging.json`,
so that it will be accessible to the app at runtime.

Note 1: Firebase is only used for user authentication.

Note 2: Firebase credentials are not committed in this repo

### Running the app

You can try and run the app.

To do so, the docker-compose.yml defines the following containers :
 - A database, called "database"
 - A Node, called "node", that will run a production-like config, meaning that it performs a `yarn start`
 - A Node, called "node-lr", that will run a de-like config, meaning that it will perform a `yarn dev:watch` and mount the `src` and `utils` dirs

Each node containers depend on the database.

Which means you only have to go to the BAM stack api root dir and perform on of the following commands:
 - `docker-compose node`: for a production-like environment
 - `docker-compose node-lr`: for a debug-like environment

