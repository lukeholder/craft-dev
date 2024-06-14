
locals {
    img_version = "20220614_1"
}

packer {
    required_plugins {
        azure = {
            version = "v1.1.0"
            source  = "github.com/hashicorp/azure"
        }
    }
}

source "azure-arm" "wireguard-proxy" {
  subscription_id = "74077769-a5e5-4a70-a105-188b276b9fa1"
  azure_tags = {
      packer = "true"
  }

  managed_image_resource_group_name = "production-TeenTixWebsite"
  managed_image_name = "wireguard-proxy-${local.img_version}"

  os_type = "Linux"
  image_publisher = "canonical"
  image_offer = "0001-com-ubuntu-server-jammy"
  image_sku = "22_04-lts-gen2"

  location = "West US 2"
  vm_size = "Standard_B1ls"
}

build {
    name = "wireguard-proxy"
    sources = [
        "source.azure-arm.wireguard-proxy"
    ]

    provisioner "file" {
        source = "wg0.conf"
        destination = "/tmp/wg0.conf"
    }

    provisioner "file" {
        source = "haproxy.cfg"
        destination = "/tmp/haproxy.cfg"
    }

    provisioner "file" {
        source = "setup.sh"
        destination = "setup.sh"
    }

    provisioner "shell" {
        inline = ["bash setup.sh"]
    }
}
