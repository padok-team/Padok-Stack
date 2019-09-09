# :earth_americas: Terraform

We use terraform to build cloud infrastructures:
* [EKS Kubernetes clusters and RDS databases on AWS](./aws-stack/README.md)
* [GKE Kubernetes clusters and CloudSQL databases on GCP](./gcp-stack/README.md)

## :construction: Install

To install terraform, run the following:
```shell
$ wget https://releases.hashicorp.com/terraform/0.11.14/terraform_0.11.14_linux_amd64.zip
$ unzip terraform_0.11.14_linux_amd64.zip
$ sudo mv terraform /opt/terraform_0.11.4
$ sudo ln -s /opt/terraform_0.11.4 /usr/local/bin/terraform_0.11.4
```
_We currently use terraform 0.11.4 because terraform 0.12.X introduced breaking changes and all the terraform modules we use are not currently compatible vith terraform 0.12.X_
