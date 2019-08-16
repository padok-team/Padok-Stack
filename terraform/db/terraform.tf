terraform {
  backend "gcs" {
    credentials = "/home/hadrien/Downloads/padok-training-lab-1594078d3035.json"
    bucket      = "ede7556a-9b34-4cc4-8869-4f394f649fa9"
    prefix      = "terraform/db"
  }
}
