package com.cpdms.mapper.dinning;

import java.util.List;
import org.apache.ibatis.annotations.Param;

import com.cpdms.model.dinning.ImgUrl;
import com.cpdms.model.dinning.ImgUrlExample;

/**
 * 图片路径 ImgUrlMapper
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-10-24 13:24:45
 */
public interface ImgUrlMapper {
      	   	      	      	      	      	      	      
    long countByExample(ImgUrlExample example);

    int deleteByExample(ImgUrlExample example);
		
    int deleteByPrimaryKey(String id);
		
    int insert(ImgUrl record);

    int insertSelective(ImgUrl record);

    List<ImgUrl> selectByExample(ImgUrlExample example);
		
    ImgUrl selectByPrimaryKey(String id);
		
    int updateByExampleSelective(@Param("record") ImgUrl record, @Param("example") ImgUrlExample example);

    int updateByExample(@Param("record") ImgUrl record, @Param("example") ImgUrlExample example); 
		
    int updateByPrimaryKeySelective(ImgUrl record);

    int updateByPrimaryKey(ImgUrl record);
  	  	
}