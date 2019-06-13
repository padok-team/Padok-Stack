##########################################################################################
# GCP Vars
credentials                = "/work/keys/padok-training-lab-d7eb320aae0e.json"
project_id                 = "padok-training-lab"
name                       = "bam-stack-api"
region                     = "europe-west4"

##########################################################################################
# GCP VPC Vars
#network                    = "vpc-02"
network                    = "default"
#subnets = [
#        {
#            subnet_name           = "subnet-02"
#            subnet_ip             = "10.10.10.0/24"
#            subnet_region         = "europe-west4"
#        }
#    ]
subnets = [
        {
            subnet_name           = "default"
            subnet_ip             = "10.164.0.0/20"
            subnet_region         = "europe-west4"
        }
    ]
secondary_ranges = {
        default = []
  }

##########################################################################################
# GKE Vars
zones                      = ["europe-west4-a", "europe-west4-b", "europe-west4-c"]
ip_range_pods              = ""
ip_range_services          = ""
http_load_balancing        = "false"
horizontal_pod_autoscaling = "true"
kubernetes_dashboard       = "true"
network_policy             = "true"

service_account            = "638579311193-compute@developer.gserviceaccount.com"

node_pools = [
    {
      name               = "default-node-pool"
      machine_type       = "g1-small"
      min_count          = 3
      max_count          = 3
      disk_size_gb       = 20
      disk_type          = "pd-standard"
      image_type         = "COS"
      auto_repair        = true
      auto_upgrade       = true
      service_account    = ""
      preemptible        = false
      initial_node_count = 1
    }
  ]

  node_pools_oauth_scopes = {
    all = []

    default-node-pool = [
      "https://www.googleapis.com/auth/cloud-platform",
    ]
  }

  node_pools_labels = {
    all = {}

    default-node-pool = {
      default-node-pool = "true"
    }
  }

  node_pools_metadata = {
    all = {}

    default-node-pool = {
      node-pool-metadata-custom-value = "my-node-pool"
    }
  }

  node_pools_taints = {
    all = []

    default-node-pool = [
      {
        key    = "default-node-pool"
        value  = "true"
        effect = "PREFER_NO_SCHEDULE"
      },
    ]
  }

  node_pools_tags = {
    all = []

    default-node-pool = [
      "default-node-pool",
    ]
  }
