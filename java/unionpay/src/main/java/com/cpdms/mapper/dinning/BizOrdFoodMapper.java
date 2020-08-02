package com.cpdms.mapper.dinning;

import com.cpdms.model.dinning.BizOrdFood;
import com.cpdms.model.dinning.BizOrdFoodExample;
import java.util.List;
import org.apache.ibatis.annotations.Param;

/**
 * 预定订单菜品 BizOrdFoodMapper
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-10-24 16:45:21
 */
public interface BizOrdFoodMapper {
      	   	      	      	      	      	      	      
    long countByExample(BizOrdFoodExample example);

    int deleteByExample(BizOrdFoodExample example);
		
    int deleteByPrimaryKey(String id);
		
    int insert(BizOrdFood record);

    int insertSelective(BizOrdFood record);

    List<BizOrdFood> selectByExample(BizOrdFoodExample example);
		
    BizOrdFood selectByPrimaryKey(String id);
		
    int updateByExampleSelective(@Param("record") BizOrdFood record, @Param("example") BizOrdFoodExample example);

    int updateByExample(@Param("record") BizOrdFood record, @Param("example") BizOrdFoodExample example); 
		
    int updateByPrimaryKeySelective(BizOrdFood record);

    int updateByPrimaryKey(BizOrdFood record);
  	  	
}