# -*- mode: ruby -*-
# vi: set ft=ruby :

PROJECT_NAME="symfony"

Vagrant.configure(2) do |config|
    config.vm.box = "ubuntu/trusty64"

    config.vm.hostname = "#{PROJECT_NAME}.box"
    config.vm.network "private_network", type: "dhcp"

    if Vagrant.has_plugin?("vagrant-foodshow")
        config.vm.network "forwarded_port", guest: 80, host: 8080, ngrok_proto: "http"
        config.foodshow.enabled = true
    else
        config.vm.network "forwarded_port", guest: 80, host: 8080
    end

    if Vagrant.has_plugin?("vagrant-hostmanager")
        config.hostmanager.enabled           = true
        config.hostmanager.manage_host       = true
        config.hostmanager.aliases           = %(#{PROJECT_NAME})
        config.hostmanager.ip_resolver = proc do |vm, resolving_vm|
            begin
                buffer = '';
                vm.communicate.execute("/sbin/ifconfig") do |type, data|
                  buffer += data if type == :stdout
                end

                ips = []
                ifconfigIPs = buffer.scan(/inet addr:(\d+\.\d+\.\d+\.\d+)/)
                ifconfigIPs[0..ifconfigIPs.size].each do |ip|
                    ip = ip.first

                    next unless system "ping -c1 -t1 #{ip} > /dev/null"

                    ips.push(ip) unless ips.include? ip
                end

                ips.first
            rescue StandardError => exc
                return
            end
        end
    end

end
