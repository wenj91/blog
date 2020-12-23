# mybatis或mybatis-plus json字段映射java bean类型

## JsonTypeHandler

通过JsonTypeHandler将数据库json字段数据从字符串转换成java bean

```java
// JsonTypeHandler.java
package com.github.wenj91.demo.handler;

import com.fasterxml.jackson.core.JsonProcessingException;
import com.fasterxml.jackson.databind.ObjectMapper;
import org.apache.ibatis.type.BaseTypeHandler;
import org.apache.ibatis.type.JdbcType;

import java.sql.CallableStatement;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;

public class JsonTypeHandler<T> extends BaseTypeHandler<T> {

    private static ObjectMapper objectMapper = new ObjectMapper();
    private Class<T> type;

    public JsonTypeHandler(Class<T> type) {
        if (type == null) {
            throw new NullPointerException("Type argument cannot be null");
        }
        this.type = type;
    }

    @Override
    public void setNonNullParameter(PreparedStatement ps, int i, Object parameter, JdbcType jdbcType) throws SQLException {
        ps.setString(i, toJsonString(parameter));
    }

    @Override
    public T getNullableResult(ResultSet rs, String columnName) throws SQLException {
        return parse(rs.getString(columnName));
    }


    @Override
    public T getNullableResult(ResultSet rs, int columnIndex) throws SQLException {
        return parse(rs.getString(columnIndex));
    }

    @Override
    public T getNullableResult(CallableStatement cs, int columnIndex) throws SQLException {
        return parse(cs.getString(columnIndex));
    }



    private String toJsonString(Object parameter) {
        try {
            return objectMapper.writeValueAsString(parameter);
        } catch (JsonProcessingException e) {
            e.printStackTrace();
            throw new RuntimeException(e);
        }
    }

    private T parse(String json) {
        try {
            return objectMapper.readValue(json, type);
        } catch (JsonProcessingException e) {
            e.printStackTrace();
            throw new RuntimeException(e);
        }
    }
}
```

## json格式转换

### 字段为json object

假定有一个user表：
```sql
create table test.user
(
    id    int auto_increment
        primary key,
    name  varchar(50)                  null,
    age   int                          null,
    email varchar(50)                  null,
    item longtext collate utf8mb4_bin null,
    constraint item
        check (json_valid(`item`))
);
```

其中字段名为：`item`为json类型，数据库数据为：`{"id": 2, "name":"id2 name"}`

这时候只需要新建一个ItemHandler类，来处理item字段

```java
// ItemHandler.java
package com.github.wenj91.demo.pojo.json;

import com.github.wenj91.demo.handler.JsonTypeHandler;

public class ItemHandler extends JsonTypeHandler<Item> {
    public ItemHandler() {
        super(Item.class);
    }
}
```

User实体类型如下：

```java
package com.github.wenj91.demo.pojo.entity;

import com.baomidou.mybatisplus.annotation.TableField;
import com.baomidou.mybatisplus.annotation.TableName;
import com.github.wenj91.demo.pojo.json.Item;
import com.github.wenj91.demo.pojo.json.ItemHandler;
import lombok.Data;

@Data
@TableName(value = "user", autoResultMap = true) // 这里需加上autoResultMap = true
public class User {
    private Long id;
    private String name;
    private Integer age;
    private String email;
    @TableField(typeHandler = ItemHandler.class) // 这里加上typeHandler
    private Item items;
}
```

这样就可以将json自动映射到java bean上了。

### 字段为json array

假定有一个user表：
```sql
create table test.user
(
    id    int auto_increment
        primary key,
    name  varchar(50)                  null,
    age   int                          null,
    email varchar(50)                  null,
    items longtext collate utf8mb4_bin null,
    constraint items
        check (json_valid(`items`))
);
```

其中字段名为：`items`为json类型，数据库数据为：`[{"id": 1, "name":"id1 name"}]`

这时候只需要新建一个ItemArrHandler类，来处理items字段

```java
// ItemArrHandler.java
package com.github.wenj91.demo.pojo.json;

import com.github.wenj91.demo.handler.JsonTypeHandler;

public class ItemArrHandler extends JsonTypeHandler<Item[]> {
    public ItemHandler() {
        super(Item[].class);
    }
}
```

User实体类型如下：

```java
package com.github.wenj91.demo.pojo.entity;

import com.baomidou.mybatisplus.annotation.TableField;
import com.baomidou.mybatisplus.annotation.TableName;
import com.github.wenj91.demo.pojo.json.Item;
import com.github.wenj91.demo.pojo.json.ItemArrHandler;
import lombok.Data;

@Data
@TableName(value = "user", autoResultMap = true) // 这里需加上autoResultMap = true
public class User {
    private Long id;
    private String name;
    private Integer age;
    private String email;
    @TableField(typeHandler = ItemArrHandler.class)  // 这里加上typeHandler
    private Item[] items;
}
```

这样就可以将json自动映射到java bean上了。
