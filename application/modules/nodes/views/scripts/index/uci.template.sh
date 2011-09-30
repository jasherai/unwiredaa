uci batch << EOF
set dropbear.@dropbear[0]=dropbear
set dropbear.@dropbear[0].PasswordAuth=on
set dropbear.@dropbear[0].Port=22
set firewall.@rule[0]=rule
set firewall.@rule[0].src=wan
set firewall.@rule[0].proto=tcp
set firewall.@rule[0].dest_port=ssh
set firewall.@rule[0].target=ACCEPT
set network.loopback=interface
set network.loopback.ifname=lo
set network.loopback.proto=static
set network.loopback.ipaddr=127.0.0.1
set network.loopback.netmask=255.0.0.0
set network.lan=interface
set network.lan.type=bridge
set network.lan.ifname="wlan0 tap0"
set network.wan=interface
set network.wan.ifname=eth0
<?php if ($this->node->getSettings()->getDhcp()) : ?>
set network.wan.proto=dhcp
<?php else : ?>
set network.wan.proto=static
set network.wan.ipaddr=<?php echo $this->node->getSettings()->getIpaddress() . "\n"; ?>
set network.wan.netmask=<?php echo $this->node->getSettings()->getNetmask() . "\n"; ?>
set network.wan.gateway=<?php echo $this->node->getSettings()->getGateway() . "\n"; ?>
set network.wan.dns="<?php echo $this->node->getSettings()->getDnsservers() . "\"\n"; ?>
<?php endif; // dhcp ?>
set ntpclient.@ntpserver[0]=ntpserver
set ntpclient.@ntpserver[0].hostname=0.at.pool.ntp.org
set ntpclient.@ntpserver[0].port=123
set ntpclient.@ntpserver[1]=ntpserver
set ntpclient.@ntpserver[1].hostname=1.at.pool.ntp.org
set ntpclient.@ntpserver[1].port=123
set ntpclient.@ntpserver[2]=ntpserver
set ntpclient.@ntpserver[2].hostname=2.at.pool.ntp.org
set ntpclient.@ntpserver[2].port=123
set ntpclient.@ntpserver[3]=ntpserver
set ntpclient.@ntpserver[3].hostname=3.at.pool.ntp.org
set ntpclient.@ntpserver[3].port=123
set ntpclient.@ntpdrift[0]=ntpdrift
set ntpclient.@ntpdrift[0].freq=0
set ntpclient.@ntpclient[0]=ntpclient
set ntpclient.@ntpclient[0].interval=600
delete openvpn.custom_config
delete openvpn.sample_server
delete openvpn.sample_client
set openvpn.management=openvpn
set openvpn.management.enable=1
set openvpn.management.client=1
set openvpn.management.dev=tun
set openvpn.management.proto=udp
set openvpn.management.remote=portal.wlan.skiamade.com
set openvpn.management.port=1195
set openvpn.management.resolv_retry=infinite
set openvpn.management.nobind=1
set openvpn.management.persist_key=1
set openvpn.management.persist_tun=1
set openvpn.management.ca=/etc/openvpn/ca.crt
set openvpn.management.cert=/etc/openvpn/client.crt
set openvpn.management.key=/etc/openvpn/client.key
set openvpn.management.comp_lzo=1
set openvpn.management.verb=3
set openvpn.management.keepalive="10 60"
set openvpn.clients=openvpn
set openvpn.clients.enable=1
set openvpn.clients.client=1
set openvpn.clients.dev=tap
set openvpn.clients.proto=udp
set openvpn.clients.remote=portal.wlan.skiamade.com
set openvpn.clients.port=1194
set openvpn.clients.resolv_retry=infinite
set openvpn.clients.nobind=1
set openvpn.clients.persist_key=1
set openvpn.clients.persist_tun=1
set openvpn.clients.ca=/etc/openvpn/ca.crt
set openvpn.clients.cert=/etc/openvpn/client.crt
set openvpn.clients.key=/etc/openvpn/client.key
set openvpn.clients.comp_lzo=1
set openvpn.clients.verb=3
set openvpn.clients.keepalive="10 60"
set system.@system[0]=system
set system.@system[0].hostname="<?php echo str_replace(array(':','-'), '', $this->node->getMac()) . "\"\n"; ?> 
set system.@system[0].zonename=Europe/Vienna
set system.@system[0].timezone=CET-1CEST,M3.5.0,M10.5.0/3
set system.@button[0]=button
set system.@button[0].button=reset
set system.@button[0].action=released
set system.@button[0].handler="logger reboot"
set system.@button[0].min=0
set system.@button[0].max=4
set system.@button[1]=button
set system.@button[1].button=reset
set system.@button[1].action=released
set system.@button[1].handler="logger factory default"
set system.@button[1].min=5
set system.@button[1].max=30
set wireless.radio0=wifi-device
set wireless.radio0.type=mac80211
set wireless.radio0.channel=<?php echo $this->node->getSettings()->getChannel() . "\n"; ?>
set wireless.radio0.macaddr=<?php echo $this->node->getMac() . "\n"; ?>
set wireless.radio0.hwmode=11ng
set wireless.radio0.htmode=HT20
set wireless.radio0.ht_capab="SHORT-GI-20 SHORT-GI-40 TX-STBC RX-STBC1 DSSS_CCK-40"
set wireless.@wifi-iface[0]=wifi-iface
set wireless.@wifi-iface[0].device=radio0
set wireless.@wifi-iface[0].network=lan
set wireless.@wifi-iface[0].mode=ap
set wireless.@wifi-iface[0].ssid="<?php echo $this->node->getSettings()->getSsid() . "\"\n"; ?>
set wireless.@wifi-iface[0].isolate=1
set wireless.@wifi-iface[0].encryption=none
set wireless.@wifi-iface[0].acct_server=172.31.0.1
set wireless.@wifi-iface[0].acct_port=1645
set wireless.@wifi-iface[0].acct_secret=titss4hostapd
set wireless.@wifi-iface[0].nasid=<?php echo str_replace(array(':','-'), '', $this->node->getMac()) . "\n"; ?> 
set qos.wan=interface
set qos.wan.classgroup=Default
set qos.wan.enabled=1
set qos.wan.overhead=1
set qos.wan.upload=<?php echo $this->node->getSettings()->getBandwidthup() . "\n"; ?>
set qos.wan.download=<?php echo $this->node->getSettings()->getBandwidthdown() . "\n"; ?>
set qos.@classify[0]=classify
set qos.@classify[0].target=Bulk
set qos.@classify[0].layer7=edonkey
set qos.@classify[1]=classify
set qos.@classify[1].target=Bulk
set qos.@classify[1].layer7=bittorrent
set qos.@classify[2]=classify
set qos.@classify[2].target=Priority
set qos.@classify[2].ports=22,53
set qos.@classify[3]=classify
set qos.@classify[3].target=Normal
set qos.@classify[3].proto=tcp
set qos.@classify[3].ports="20,21,25,80,110,443,993,995"
set qos.@classify[4]=classify
set qos.@classify[4].target=Express
set qos.@classify[4].ports=5190
set qos.@default[0]=default
set qos.@default[0].target=Express
set qos.@default[0].proto=udp
set qos.@default[0].pktsize=-500
set qos.@reclassify[0]=reclassify
set qos.@reclassify[0].target=Priority
set qos.@reclassify[0].proto=icmp
set qos.@default[1]=default
set qos.@default[1].target=Bulk
set qos.@default[1].portrange=1024-65535
set qos.@reclassify[1]=reclassify
set qos.@reclassify[1].target=Priority
set qos.@reclassify[1].proto=tcp
set qos.@reclassify[1].pktsize=-128
set qos.@reclassify[1].mark=!Bulk
set qos.@reclassify[1].tcpflags=SYN
set qos.@reclassify[2]=reclassify
set qos.@reclassify[2].target=Priority
set qos.@reclassify[2].proto=tcp
set qos.@reclassify[2].pktsize=-128
set qos.@reclassify[2].mark=!Bulk
set qos.@reclassify[2].tcpflags=ACK
set qos.Default=classgroup
set qos.Default.classes="Priority Express Normal Bulk"
set qos.Default.default=Normal
set qos.Priority=class
set qos.Priority.packetsize=400
set qos.Priority.maxsize=400
set qos.Priority.avgrate=10
set qos.Priority.priority=20
set qos.Priority_down=class
set qos.Priority_down.packetsize=1000
set qos.Priority_down.avgrate=10
set qos.Express=class
set qos.Express.packetsize=1000
set qos.Express.maxsize=800
set qos.Express.avgrate=50
set qos.Express.priority=10
set qos.Normal=class
set qos.Normal.packetsize=1500
set qos.Normal.packetdelay=100
set qos.Normal.avgrate=10
set qos.Normal.priority=5
set qos.Normal_down=class
set qos.Normal_down.avgrate=20
set qos.Bulk=class
set qos.Bulk.avgrate=1
set qos.Bulk.packetdelay=200
<?php
    if ($this->node->getSettings()->getActivefrom() > 0 && $this->node->getSettings()->getActiveto() > 0) :
?>
set crontabs.@crontab[1].hours=<?php echo (int) $this->node->getSettings()->getActivefrom(); ?>

set crontabs.@crontab[1].enabled=1
set crontabs.@crontab[2].hours=<?php echo (int) $this->node->getSettings()->getActiveto(); ?>

set crontabs.@crontab[2].enabled=1
<?php
    else :
?>
set crontabs.@crontab[1].hours=*
set crontabs.@crontab[1].enabled=0
set crontabs.@crontab[2].hours=*
set crontabs.@crontab[2].enabled=0
<?php
    endif;
    
	if ($this->node->getSettings()->getTrafficlimit() > 0) :
?>
set custom.limits=traffic
set custom.limits.traffic=4000
<?php
	endif;
?>
EOF
