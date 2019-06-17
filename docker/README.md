# NOTE

Include or link to staffme/dockerisation README.md.

# Dockerize BAM stack

This project is about making the BAM stack run in a Dockerized environment.

We will proceed in 2 steps :
 1. Init a BAM stack, using the BAM method and repo (Careful: we won't use all steps of the BAM method)
 2. Add / modify the required files so that the stack will run in Docker

## Init a new BAM stack

You will use the process bellow in order to initiate the BAM stack.

**Notes:**
- Before using setup.sh, you need to comment the following lines in the script :
   - If you don't want to push the new repo to BAM GitHub repos :
      - 31: if ! [ -x "$(command -v hub)" ]; then
      - 32:   echo "Hub is not installed. You'll need to create the GitHub project $NAME manually." >&2
      - 33: else
      - 34:   hub create -p "bamlab/$NAME"
      - 35:   git push -u origin master
      - 36: fi

   - If you don't want travis config :
      - 44: echo "Opening GitHub to configure Travis... Add $API_PROJECT_NAME and $BACKOFFICE_PROJECT_NAME to the list of selected repositories."
      - 45: open "https://github.com/organizations/bamlab/settings/installations/209171"

- When the script asks you where you want your app to be hosted, answer the following :
      - "skip"

The process is the following:
1. $ git clone https://github.com/bamlab/create-bam-stack.git <project_name>
2. $ cd <project_name>
3. \# Edit the setup.sh script as described above
4. $ ./setup.sh
    - Answer to the first question with your project's name
    - Say "yes" to api generation
    - Say "yes" to backoffice generation
    - **Say "skip" to hosting configuration**
5. \# Remove every file and sub-dir whose name doesn't match <project_name>-* expression
    - Indeed, the `setup.sh` script create to GIT repos, and from now one we won't need the other files anymore
6. $ grep -r myproject <project_name>-* | cut -d ':' -f 1 | sort | uniq | xargs sed -i 's:myproject:<project_name>:g'
7. $ grep -r MyProject <project_name>-* | cut -d ':' -f 1 | sort | uniq | xargs sed -i 's:MyProject:<beautiful_project_name>:g'

**Note:** the two command above perform the following actions:
 1. Find occurrences of myproject (recp. MyProject) in the newly created GIT repos
 2. Cut the lines in order to keep only the file names
 3. Sort the filename (although it may already be properly sorted)
 4. keep only one occurrence of each file name
 5. Make a substitution on every files, replacing myproject (resp. MyProject) by your project name (resp. a more beautiful version of the project name)
     - More beautiful doesn't mean exotic: please stick to aplha-numerical characters.

**Note for Mac users:** These command may fail. In that case you can replace these patterns using your IDE instead.

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

**Note:** The last one is stored as a regular file in the initial repo, but has to be copied as a hidden file.

These files add the Docker logic to the project so that you can perform a `docker-compose up` commands.

### Config files

Any command may still fail however if you don't set the proper configuration values.

In order to do that you will use the config example available in the bam-stack-api repo (so not the one you just created!)
 - Remove any files in `./config` dir
 - Using the config exemples in `./config` in this repo customize the config files in the BAM stack api project

**Note:** the `./config/default.yaml` file available in this repo should be enough to perform a PoC test.

### Firebase config

At this point running the appli will still fail because you need Firebase credentials to make it work.
Which mean you need a Firebase project:
 - Go there to create a new project: https://console.firebase.google.com/?pli=1
 - In Authentication, connection modes, enable email/password
 - In the Firebase project parameters / account services, generate a new private key
 - Copy it in ./config/firebase-admin.staging.json
    - Note that you can change the expected filename in the app config you built earlier
 - In Firebase / Authentication, create a new user with your email
 - Add the user to the seeds / user with the admin role (**Not tested**)

**Note:**
 - Firebase is only used for user authentication.
 - Firebase credentials are not committed in this repo

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

