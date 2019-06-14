terraform {
  backend "gcs" {
    credentials = "/work/keys/padok-training-lab-d7eb320aae0e.json"
    bucket      = "ede7556a-9b34-4cc4-8869-4f394f649fa9"
    prefix      = "terraform/db"
  }
}
