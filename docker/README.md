# NOTE & questions

**Questions:**
- What file names are accepted for the api's config ?

Include or link to staffme/dockerisation README.md.

# Dockerize BAM stack

This project is about making the BAM stack run in a Dockerized environment.

We will proceed in 2 steps :

1. **Init a BAM stack**, using the BAM method and repo (Careful: we won't use all steps of the BAM method)
2. **Add / modify the required files** so that the stack will run in Docker

## About multpiple Git repos and their naming

As you may already know (or not), the create-bam-stack git repo a kind of bootstrapping repo for BAM stack project.

So, this repo, through the usage of its `setup.sh` script, will initiate two other git repos,
each pre-populated with a set of sources from the create-bam-stack repo itself.

Those two repos will be dedicates to two different part of the new project:

1. **The API**
2. **The backoffice**

And then there is the repo whose README you'r currently reading ;-)
So that's 4 different repos...

For clarity purposes, and in order not to take one repo for an other during this procedure,
they will be refered in the next part as follow:

- **repo-padok** will be the one containing this readme
- **repo-bam** will be the repo that **bootstrap** new BAM projects
- For the new BAM project
  - **repo-api** will be the **API**-related repo
  - **repo-bo** will be the backoffice-related repo

## About variables

We will use variable in this README to make it as general as possible.

Variables will always be noted between a `<` and a `>`.

For exemple the variable whose name is "toto" will be written `<toto>` in any section, any paragraph or code sample.

Among the variable that we will use, we can identify:
- **<project_name>:** your project's name, written in a raw form (no spaces)
- **<beautiful_project_name>:** also your project name but maybe camel cased so it will be easier to read (no spaced)

**Note:**
- Repos are a special case of variable. To simplify the writting we will use for example **"repo-padok"** to indicate both the **repo** and **its path**.
  The goes for the other repos names.
  So you may find "repo-padok" written in code sample, and in this case it means the path to the repo-padok, and note just the string "repo-padok"

## Init a new dockerized BAM stack

### Building the new repos

You will use the process bellow in order to initiate the BAM stack,
and therefore repo-api and repo-bo.

First, clone **repo-bam** in an new directory named after your project,
and go inside it:
```bash
$ git clone https://github.com/bamlab/create-bam-stack.git <project_name>
$ cd <project_name>
```

Now you will use the `repo-bam/setup.sh` script to create **repo-api** and **repo-bo**.

But before you can use it, you may to make some customize the script a little:

- Comment the following lines in the script:
  - If you don't want to push the new repo to BAM GitHub repos:
```bash
    31: #if ! [ -x "$(command -v hub)" ]; then
    32: #  echo "Hub is not installed. You'll need to create the GitHub project $NAME manually." >&2
    33: #else
    34: #  hub create -p "bamlab/$NAME"
    35: #  git push -u origin master
    36: #fi
```
  - If you don't want travis config:
```bash
    43: #echo "Opening GitHub to configure Travis... Add $API_PROJECT_NAME and $BACKOFFICE_PROJECT_NAME to the list of selected repositories."
    44: #open "https://github.com/organizations/bamlab/settings/installations/209171"
```

Then run the script:

```bash
$ ./setup.sh

# Answer to questions:
#  - Answer to the first question with your project's name
#  - Say "yes" to api generation
#  - Say "yes" to backoffice generation
#  - **Say "skip" to hosting configuration**
```

Now if you look inside the current dir you will an output like the following:

```bash
$ ls -l .

  total 64
  drwxrwxr-x 7 remi remi 4096 juin  20 14:20 .
  drwxrwxr-x 9 remi remi 4096 juin  20 14:19 ..
  drwxrwxr-x 8 remi remi 4096 juin  20 14:20 api
  drwxrwxr-x 5 remi remi 4096 juin  20 14:20 backoffice
  -rw-rw-r-- 1 remi remi    7 juin  20 14:19 .bam-stack-version
  -rw-rw-r-- 1 remi remi 2017 juin  20 14:19 CHANGELOG.md
  drwxrwxr-x 4 remi remi 4096 juin  20 14:19 devops
  -rw-rw-r-- 1 remi remi   96 juin  20 14:19 .gitattributes
  -rw-rw-r-- 1 remi remi   89 juin  20 14:20 .gitignore
  -rw-rw-r-- 1 remi remi  117 juin  20 14:20 project.conf
  -rw-rw-r-- 1 remi remi 6678 juin  20 14:19 README.md
  -rwxr-xr-x 1 remi remi 1652 juin  20 14:20 setup.sh
  drwxrwxr-x 9 remi remi 4096 juin  20 14:20 <project_name>-api
  drwxrwxr-x 6 remi remi 4096 juin  20 14:20 <project_name>-backoffice
  -rw-rw-r-- 1 remi remi  901 juin  20 14:19 .travis.yml
```

Great ! As you can see around the end of the output there two new directories, corresponding to two new repos:

 - **<project_name>-api:** it's **the repo-api**
 - **<project_name>-backoffice:** it's the **repo-bo**

From now on we won't need the **repo-bam** any more,
so you can clean up this dir by removing any file (including hidden ones) whose name doesn't start with "<project_name>",
for example with the following command:

```bash
# The command bellow can be explained like this:
#  - First "ls -l | grep -v testtest will output the name of the files and dirs to be removed
#  - Then "xargs rm -r" will deletes every file / dir provided through its standard input

$ ls -a | grep -v testtest | xargs rm -r
```

**Note:** This command will try to delete `.` and `..` dirs and will obviously fail in doing so, but that's to be expected and not a big deal.

**Tip:** Don't hesitate to check what file will be deleted with a first run without the "xargs..." part.

Now that repo-api and repo-bo are created you need to customize them a little bit more by replacing some patterns.

If you'r using VSCode or any other IDE, you can use its refactoring capabilities.

If you are not, you can also use a commands like the following:

```bash
# **Note:** the two commands bellow perform the following actions:
#  - Find occurrences of myproject (recp. MyProject) in the newly created GIT repos
#  - Cut the lines in order to keep only the file names
#  - Sort the filename (although it may already be properly sorted)
#  - keep only one occurrence of each file name
#  - Make a substitution on every files, replacing myproject (resp. MyProject) by your project name (resp. a more beautiful version of the project name)
#     - More beautiful doesn't mean exotic: please stick to aplha-numerical characters.

$ grep -r myproject <project_name>-* | cut -d ':' -f 1 | sort | uniq | xargs sed -i 's:myproject:<project_name>:g'
$ grep -r MyProject <project_name>-* | cut -d ':' -f 1 | sort | uniq | xargs sed -i 's:MyProject:<beautiful_project_name>:g'
```

**Note for Mac users:** These command may fail. In that case you can replace these patterns using your IDE instead.

You should en up with the following in the current dir:

```bash
$ ls -l

  total 8
  drwxrwxr-x 9 remi remi 4096 juin  20 14:20 testtest-api
  drwxrwxr-x 6 remi remi 4096 juin  20 14:20 testtest-backoffice
```

Ok the, now that you have BAM-like repos you are ready to dockerize them.

### Dockerize the BAM stack

Everything in the following sections will only concern the **repo-api** git project created during the previous steps,
and located in the `<project_name>-api` sub-directory.

#### Docker-related files

Copy the following files to the root dir of your new BAM stack api :

- `repo-padok/docker/Docker-compose.yml` -> `repo-api/docker-compose.yml`
- `repo-padok/docker/Dockerfile`         -> `repo-api/Dockerfile`
- `repo-padok/docker/dockerignore`       -> `repo-api/.dockerignore` (don't forget the dot !)

**Note:** The last one is stored as a regular file in the initial repo, but has to be copied as a hidden file.

**Careful:** Older versions of these files may already present in repo-api, but you need to overwrite them anyway.

These files add the Docker logic to the project so that you can perform a `docker-compose up` commands.

#### Config files

Any command may still fail however if you don't set the proper configuration values.

In order to do that you will use the config example available in the repo-padok:

```bash
$ rm repo-api/config/*
$ cp repo-padok/docker/config/default.yaml repo-api/config
```

**Note:** the `repo-padok/config/default.yaml` file available in this repo should be enough to perform a PoC test as well build more complex use cases.

#### Firebase config

At this point running the appli will still fail because you need Firebase credentials to make it work.
Which mean you need a Firebase project:

- Go there to create a new project: [firebase](https://console.firebase.google.com/?pli=1)
- Create a new project
- In the **project page** -> **Authentication** -> **connection modes** -> **enable email/password**
- In the Firebase project **parameters / account services**, generate a new private key
- Copy it in `repo-api/config/firebase-admin.staging.json`
  - Note that you can change the expected filename in the app config you built earlier to match the name of the actual file
- In **Firebase / Authentication**, create a new user with your email
- Add the user to the seeds / user with the admin role (**Not tested, not necessary for test purposes**)

**Note:**

- Firebase is only used for user authentication.
- Firebase credentials are not committed in this repo

Now you should have everything you need to run the docker containers :-)

#### Running the app

So you can try and run the app.

To do so, the `repo-api/docker-compose.yml` file you just copied defines the following containers :

- A database, called "database"
- A Node, called "node", that will run a production-like config
  - meaning that it performs a `yarn start`
  - and has "database" as a dependency
- A Node, called "node-lr", that will run a de-like config
  - meaning that it will perform a `yarn dev:watch`,
  - mount the `src` and `utils` dirs,
  - re-build + reload the app if you change any file in `src` or `utils` dirs,
  - and has "database" as a dependency

Each node containers depend on the database, so you only need to ask compose to launch a node and the database will be launched before automatically

Which means you only have to go to the repo-api dir and perform on of the following commands:

 - `docker-compose node`: for a production-like environment
 - `docker-compose node-lr`: for a debug-like environment

For example, if you want to launch the node-lr version

```bash
$ cd repo-api
$ docker-compose node-lr
```

**Tip:** Just ad `-d` flag if you don't want docker-compose to stay in the foreground

```bash
$ docker-compose -d node-lr
```

## Test the app

If every went fine in the previous part and you api is now listenning, let's say on port 3000.

You can check if it's working with a curl:

```bash
$ curl -i localhost:3000
```

The header of the answer in the output should indicate `200 OK` and you should see `OK` in the answer's body.

Then for a more graphical test, use your browser with the following URL: `http://localhost:3000/graphql`
