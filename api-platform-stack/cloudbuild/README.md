# Getting Helm cloud buider

https://github.com/GoogleCloudPlatform/cloud-builders-community/tree/master/helm

`git clone https://github.com/GoogleCloudPlatform/cloud-builders-community.git`

**Careful being in the good project**
`gcloud builds submit . --config=cloudbuild.yaml`

# Submit a api-platform stack cloud build

## Configuring kubectl

One step is about configuring kubetcl in the workspace,
by giving it the proper information through env vars and asking it for cluster info,
with the custer-info command.

**Required info:**

- Project name
- Cluster name
- One of:
  - region
  - zone

Problem:

- ERROR: (gcloud.container.clusters.get-credentials) ResponseError: code=403, message=Required "container.clusters.get" permission(s) for "projects/padok-training-lab/locations/europe-west4/clusters/api-platform-stack". See https://cloud.google.com/kubernetes-engine/docs/troubleshooting#gke_service_account_deleted for more info.
  Error from server (Forbidden): services is forbidden: User "638579311193@cloudbuild.gserviceaccount.com" cannot list resource "services" in API group "" in the namespace "kube-system": Required "container.services.list" permission.

  Other web page: https://cloud.google.com/cloud-build/docs/securing-builds/set-service-account-permissions
  -> Cloud build service accounts - <PROJECT_NUMBER>@cloudbuild.gserviceaccount.com - service-<PROJECT_NUMBER>@gcp-sa-cloudbuild.iam.gserviceaccount.com
  Seems to be service account but are not present in the "service account" admin page...
  In this case the first one needed un upgrade from IAM page to add the following roles: - "Lecteur de cluster Kubernetes Engine" - "Lecteur Kubernetes Engine"

- Error: forwarding ports: error upgrading connection: pods "tiller-deploy-6d65d78679-smt22" is forbidden: User "638579311193@cloudbuild.gserviceaccount.com" cannot create resource "pods/portforward" in API group "" in the namespace "kube-system": Required "container.pods.portForward" permission.

  Still the same problem, this time you need to add to the same account the role: "Administrateur de Kubernetes Engine"

- Helm step: [debug] SERVER: "127.0.0.1:36025"
  Error: UPGRADE FAILED: "api-v1-release" has no deployed releases
  UPGRADE FAILED
  Error: "api-v1-release" has no deployed releases

  -> I's because a release called "api-v1-release" already exist, but is not deployed
  Try `Helm list -a` to show this kind of artifact and `Helm delete <release_name> --purge` to delete them
