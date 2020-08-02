package com.cpdms.service;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

import com.cpdms.mapper.custom.SysUtilDao;
import com.cpdms.model.custom.SQLAdapter;

@Service
public class SysUtilService {
	@Autowired
	private SysUtilDao dao;
	
	/**
	 * 执行sql
	 * @param sql
	 * @return
	 * @author fuce
	 * @Date 2019年8月31日 下午6:15:08
	 */
	public int executeSQL(String sql){
		return dao.executeSQL(new SQLAdapter(sql));
	}
}
