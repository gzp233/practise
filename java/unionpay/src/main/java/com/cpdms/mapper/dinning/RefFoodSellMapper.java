package com.cpdms.mapper.dinning;

import com.cpdms.model.dinning.RefFoodSell;
import com.cpdms.model.dinning.RefFoodSellExample;
import com.cpdms.model.dinning.RefFoodTime;

import java.util.List;
import org.apache.ibatis.annotations.Param;

/**
 * 菜品供应方式关系 RefFoodSellMapper
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-10-24 16:40:08
 */
public interface RefFoodSellMapper {

    long countByExample(RefFoodSellExample example);

    int deleteByExample(RefFoodSellExample example);

    int deleteByPrimaryKey(String id);

    int deleteBySellIdAndFoodId(@Param(value = "sellTypeId") String sellTypeId,@Param(value = "foodId") String foodId);

    int insert(RefFoodSell record);

    int insertSelective(RefFoodSell record);

    List<RefFoodSell> selectByExample(RefFoodSellExample example);

    RefFoodSell selectByPrimaryKey(String id);

    List<RefFoodSell> selectByFoodId(String id);

    int updateByExampleSelective(@Param("record") RefFoodSell record, @Param("example") RefFoodSellExample example);

    int updateByExample(@Param("record") RefFoodSell record, @Param("example") RefFoodSellExample example);

    int updateByPrimaryKeySelective(RefFoodSell record);

    int updateByPrimaryKey(RefFoodSell record);

}
