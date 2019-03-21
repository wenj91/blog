yum remove docker docker-common docker-selinux docker-engine -y
/etc/systemd -name '*docker*' -exec rm -f {} ;
find /etc/systemd -name '*docker*' -exec rm -f {} \;
find /lib/systemd -name '*docker*' -exec rm -f {} \;