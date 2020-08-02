package com.cpdms.mapper.custom;

import com.cpdms.model.custom.SQLAdapter;

/**
 * 通用dao
* @ClassName: SysUtilDao
* @author fuce
* @date 2019-08-31 18:11
*
 */
public interface SysUtilDao {
	
	/**
	 * 执行sql
	 * @param sql
	 * @return
	 * @author fuce
	 * @Date 2019年8月31日 下午6:15:08
	 */
	public int executeSQL(SQLAdapter sql); 
}
