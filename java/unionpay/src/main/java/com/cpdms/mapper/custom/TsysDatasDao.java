package com.cpdms.mapper.custom;

import java.util.List;

import org.apache.ibatis.annotations.Param;

import com.cpdms.model.auto.TsysDatas;

public interface TsysDatasDao {
	
	public List<TsysDatas> selectByPrimaryKeys(@Param("ids") List<String> ids);
	
}