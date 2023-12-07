#!/bin/bash
# 备份目标数据库的名称
DATABASE_NAME="test"
DATABASE_PASSWORD="123456"

# 备份文件保存路径
BACKUP_DIR="/var/mysqlDataBackup"

# 备份文件命名格式
BACKUP_FILE_NAME="${BACKUP_DIR}/${DATABASE_NAME}_$(date +%Y%m%d_%H%M%S).sql"

COMPRESSED_FILE_NAME="${BACKUP_DIR}/${DATABASE_NAME}_$(date +%Y%m%d_%H%M%S).sql.gz"
# MySQL容器名称
MYSQL_CONTAINER_NAME="mysql"

# 导出数据库备份
docker exec ${MYSQL_CONTAINER_NAME} mysqldump -u root -p${DATABASE_PASSWORD} ${DATABASE_NAME}| gzip > ${BACKUP_FILE_NAME}

gzip -c ${BACKUP_FILE_NAME} > ${COMPRESSED_FILE_NAME}

rm ${BACKUP_FILE_NAME}
# 可选：根据需求自定义备份文件的保留周期和清理逻辑

# 设置备份文件保留天数
DAYS_TO_KEEP=3

# 清理旧的备份文件
find ${BACKUP_DIR} -name "${DATABASE_NAME}_*.sql.gz" -mtime +${DAYS_TO_KEEP} -exec rm {} \;
