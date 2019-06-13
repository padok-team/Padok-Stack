#module "vpc" {
#  source       = "terraform-google-modules/network/google"
#  version      = "0.6.0"
#  project_id   = "${var.project_id}"
#  network_name = "${var.network}"
#
#  subnets          = "${var.subnets}"
#  secondary_ranges = "${var.secondary_ranges}"
#}

module "gke" {
  source                     = "terraform-google-modules/kubernetes-engine/google"
  project_id                 = "${var.project_id}"
  name                       = "${var.name}"
  region                     = "${var.region}"
  zones                      = "${var.zones}"
  #network                    = "${module.vpc.network_name}"
  network                    = "${var.network}"
  # Trying to use the default VPC
  subnetwork                 = "${lookup(var.subnets[0], "subnet_name")}"
  #subnetwork                 = "${module.vpc.subnets_names[0]}"
  ip_range_pods              = "${var.ip_range_pods}"
  ip_range_services          = "${var.ip_range_services}"
  http_load_balancing        = "${var.http_load_balancing}"
  horizontal_pod_autoscaling = "${var.horizontal_pod_autoscaling}"
  kubernetes_dashboard       = "${var.kubernetes_dashboard}"
  network_policy             = "${var.network_policy}"
  node_pools                 = "${var.node_pools}"
  node_pools_oauth_scopes    = "${var.node_pools_oauth_scopes}"
  node_pools_labels          = "${var.node_pools_labels}"
  node_pools_metadata        = "${var.node_pools_metadata}"
  node_pools_taints          = "${var.node_pools_taints}"
  node_pools_tags            = "${var.node_pools_tags}"

  service_account            = "${var.service_account}"
}
