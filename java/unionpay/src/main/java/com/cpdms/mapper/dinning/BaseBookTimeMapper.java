package com.cpdms.mapper.dinning;

import com.cpdms.model.dinning.BaseBookTime;
import com.cpdms.model.dinning.BaseBookTimeExample;
import java.util.List;
import org.apache.ibatis.annotations.Param;

/**
 * 预定时间 BaseBookTimeMapper
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-10-24 16:48:30
 */
public interface BaseBookTimeMapper {
      	   	      	      	      	      	      	      	      	      	      
    long countByExample(BaseBookTimeExample example);

    int deleteByExample(BaseBookTimeExample example);
		
    int deleteByPrimaryKey(String id);
		
    int insert(BaseBookTime record);

    int insertSelective(BaseBookTime record);

    List<BaseBookTime> selectByExample(BaseBookTimeExample example);
		
    BaseBookTime selectByPrimaryKey(String id);
		
    int updateByExampleSelective(@Param("record") BaseBookTime record, @Param("example") BaseBookTimeExample example);

    int updateByExample(@Param("record") BaseBookTime record, @Param("example") BaseBookTimeExample example); 
		
    int updateByPrimaryKeySelective(BaseBookTime record);

    int updateByPrimaryKey(BaseBookTime record);
  	  	
}