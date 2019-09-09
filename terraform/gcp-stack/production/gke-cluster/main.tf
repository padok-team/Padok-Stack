terraform {
  backend "gcs" {
    credentials = "../../<production_project_name>-terraform-credentials.json"
    bucket      = "<production_project_name>-terraform-state"
    prefix      = "terraform/cluster"
  }
}

module "<company_name>_cluster" "<production_project_name>_cluster" {
  source                     = "../../modules/gke-cluster"

  project_id                 = "${var.project_id}"
  name                       = "gke-cluster"
  zones                      = [
    "europe-west4-a",
    "europe-west4-b",
    "europe-west4-c"
  ]
  network                    = "default"
  subnets                    = [
    {
      subnet_name            = "default"
      subnet_ip              = "10.164.0.0/20"
      subnet_region          = "europe-west4"
    }
  ]

  secondary_ranges           = {
    default                  = []
  }

  ip_range_pods              = ""
  ip_range_services          = ""
  http_load_balancing        = "true"
  horizontal_pod_autoscaling = "true"
  kubernetes_dashboard       = "true"
  network_policy             = "true"


  node_pools = [
    {
      name                   = "node-pool"
      machine_type           = "g1-small"
      min_count              = 0
      max_count              = 1
      disk_size_gb           = 10
      disk_type              = "pd-standard"
      image_type             = "COS"
      auto_repair            = true
      auto_upgrade           = true
      service_account        = ""
      preemptible            = false
      initial_node_count     = 0
    }
  ]

  node_pools_oauth_scopes    = {
    all                      = []
    node-pool                = [
      "https://www.googleapis.com/auth/cloud-platform",
    ]
  }

  node_pools_labels          = {
    all                      = {}
    node-pool                = {
      default-pool           = "true"
    }
  }

  node_pools_metadata        = {
    all                      = {}
    node-pool                = {
      node-pool-metadata-custom-value = "my-node-pool"
    }
  }

  node_pools_taints          = {
    all                      = []
    node-pool                = [
      {
        key                  = "node-pool"
        value                = "true"
        effect               = "PREFER_NO_SCHEDULE"
      },
    ]
  }

  node_pools_tags            = {
    all                      = []
    node-pool                = [
      "node-pool",
    ]
  }

  remove_default_node_pool   = "true"

  service_account            = "terraform@<production_project_name>.iam.gserviceaccount.com"
}
