# Deploy the bam-stack-api in a GKE Kubernetes cluster using Helm

The aim of this part is to install Helm, configure it and build required templates
in order to Helm-package the dockerized version of the application.

## Prerequisites

This part require:
 - Done with Terraform ([here](../terraform/README.md))
    - A fully deployed GKE Kubernetes cluster
    - A GCP database
    - Proper NEtwork configuration so that apps in the cluster are able to communicate with the database
 - Done with Docker ([here](../docker/README.md))
    - The docker version of the bam-stack-api
    - Firebase authentication

## Install and init Helm

### Prequesite

Be careful not to install Helm to quickly!

Indeed, if you install Tiller right now, it will use the `default` service account.
And that will prevent Tiller it from working properly... And even from resetting itself...

So you first need to create a new account in the kube-system namespace and allow it the proper permission,
using the file `./tiller_auth.yml` and the command `kubectl apply -f ./tiller_auth.yml`.

This will create the account to be used by Tiller: `tiller`.

### Install

Download Helm from:
 - https://github.com/helm/helm/releases

And put into `/opt` directory.

Then perform the following commands:
 - cd /opt
 - mkdir helm
 - cd helm
 - untar -xzf ../helm-v<VERSION_INFO>.tar.gz
    - Replace <VERSION_INFO> by your version information:
       - Version number
       - Distribution
       - Architecture
 - rm ../helm-v<VERSION_INFO>.tar.gz
 - ln -s /opt/helm/helm /usr/local/bin/helm

### Init

For test purposes you can just perform the following command:
 - `Helm init --service-account tiller`

Careful: As you can see, we Helm to initialize Tiller with the service account we created earlier. 

`Helm` will look at you kubectl config and install `Tiller` in the cluster
within the `kube-system` namespace.

TODO: add a command that is closer to what we'll do in production environments

## Build Helm-related Kubernetes files

### Build the image

With a `cloudbuild.yaml` file and the following command:
 - `gcloud builds submit --config cloudbuild.yaml --substitutions=COMMIT_SHA=test`

### Create a chart

First init the Chart with `Helm create <chart_name>`.
Then clean up the init dir by removing everything you won't need.
Add your own YAML files.

Test the chart with:
 - `Helm template <chart_dir> -x templates/<template_filename>`
     -> If your template is not valid, you may have uneasy error messages, that's the way it is...
 - `Helm lint` (TODO)

### Secret management

Bam-stack-api require an access to Firebase project, guaranteed by a key expected in json format.

Since we ensured we had that key when making the [docker-related steps](../docker/README.md),
we only need to create a secret with it and to provide this secret to our Chart through a volume mount.

The volume mount is already configured in the `deployment.yaml` file in the chart's templates.

As for the secret, you can create it like so:
 - kubectl create secret generic firebase --from-file=firebase-key=<path_to_key>

### Deploy

Prequisite: At first we will suppose that the image <GCR_IMAGE> dockerizing the API is available.

problems:
 - system:serviceaccount:kube-system:default
    -> Solved: with a dedicated Tiller service account created in Kubernetes
 - Deployment occured in the wrong namespace...
    -> Default is for Helm to install the Chart in the current namespace
    -> This can be overriden by using install / upgrade commands with the `--namespace` option

Initial deployment:
 - helm install bam-stack-api

Delete and deploy again (without using upgrade)
 - helm delete <release-name>
 - helm install bam-stack-api