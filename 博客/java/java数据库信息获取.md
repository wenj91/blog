sqlDataTypeToKeyType.put(Types.CHAR, KeyType.STRING);
sqlDataTypeToKeyType.put(Types.LONGVARCHAR, KeyType.STRING);
sqlDataTypeToKeyType.put(Types.NUMERIC, KeyType.FLOAT);
sqlDataTypeToKeyType.put(Types.DECIMAL, KeyType.FLOAT);
sqlDataTypeToKeyType.put(Types.BIT, KeyType.BOOL);
sqlDataTypeToKeyType.put(Types.TINYINT, KeyType.INT);
sqlDataTypeToKeyType.put(Types.SMALLINT, KeyType.INT);
sqlDataTypeToKeyType.put(Types.INTEGER, KeyType.INT);
sqlDataTypeToKeyType.put(Types.BIGINT, KeyType.INT);
sqlDataTypeToKeyType.put(Types.REAL, KeyType.FLOAT);
sqlDataTypeToKeyType.put(Types.FLOAT, KeyType.FLOAT);
sqlDataTypeToKeyType.put(Types.DOUBLE, KeyType.FLOAT);
sqlDataTypeToKeyType.put(Types.BINARY, KeyType.STRING);
sqlDataTypeToKeyType.put(Types.VARBINARY, KeyType.STRING);
sqlDataTypeToKeyType.put(Types.LONGVARBINARY, KeyType.STRING);
sqlDataTypeToKeyType.put(Types.DATE, KeyType.STRING);
sqlDataTypeToKeyType.put(Types.TIME, KeyType.STRING);
sqlDataTypeToKeyType.put(Types.TIMESTAMP, KeyType.STRING);


// 加载驱动到JVM
        Class.forName("com.mysql.jdbc.Driver");
        // 获取连接
        Connection connection = DriverManager.getConnection(url, userName, passWord);
        // 数据库的所有数据
        DatabaseMetaData metaData = connection.getMetaData();
        // 获取表的主键名字
        ResultSet pkInfo = metaData.getPrimaryKeys(null, "%", "STUDENTS");
        System.out.println(pkInfo == null);
        while (pkInfo.next()){
            System.out.print("数据库名称:"+pkInfo.getString("TABLE_CAT")+"                  ");
            System.out.print("表名称:"+pkInfo.getString("TABLE_NAME")+"                  ");
            System.out.print("主键列的名称:"+pkInfo.getString("COLUMN_NAME")+"                  ");
            System.out.print("类型:"+pkInfo.getString("PK_NAME")+"                  ");
            System.out.println("");
        }
            System.out.println("------------------------------分隔符--------------------------------------------");
        // 获取表的相对应的列的名字
        ResultSet tableInfo = metaData.getColumns(null,"%", "STUDENTS", "%");
        while (tableInfo.next()){
            // 表的名字
            System.out.print("表名:"+tableInfo.getString("TABLE_NAME")+"                  ");
            // 列的名称
            System.out.print("列名:"+tableInfo.getString("COLUMN_NAME")+"                  ");
            // 默认值
            System.out.print("默认值 :"+tableInfo.getString("COLUMN_DEF")+"                  ");
            // 字段的类型
            System.out.print("字段的类型:"+tableInfo.getString("TYPE_NAME")+"                  ");
            // 是否可以为空
            System.out.print("是否可以为空:"+tableInfo.getString("IS_NULLABLE")+"                  ");
            // 是否为自增
            System.out.print("是否为自增:"+tableInfo.getString("IS_AUTOINCREMENT")+"                  ");
            // 字段说明
            System.out.print("字段说明:"+tableInfo.getString("REMARKS")+"                  ");
            // 长度(有时候是错的)
            System.out.print("长度:"+tableInfo.getString("COLUMN_SIZE")+"                  ");
            System.out.println();
        }