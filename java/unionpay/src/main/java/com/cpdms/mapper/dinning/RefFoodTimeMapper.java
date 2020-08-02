package com.cpdms.mapper.dinning;

import com.cpdms.model.dinning.RefFoodTime;
import com.cpdms.model.dinning.RefFoodTimeExample;
import java.util.List;
import org.apache.ibatis.annotations.Param;

/**
 * 菜品餐段关系 RefFoodTimeMapper
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-10-24 16:47:39
 */
public interface RefFoodTimeMapper {

    long countByExample(RefFoodTimeExample example);

    int deleteByExample(RefFoodTimeExample example);

    int deleteByPrimaryKey(String id);

    int deleteByTimeIdAndFoodId(@Param(value = "timeId") String timeId,@Param(value = "foodId") String foodId);

    int insert(RefFoodTime record);

    int insertSelective(RefFoodTime record);

    List<RefFoodTime> selectByExample(RefFoodTimeExample example);

    List<RefFoodTime> selectByFoodId(String id);

    RefFoodTime selectByPrimaryKey(String id);

    int updateByExampleSelective(@Param("record") RefFoodTime record, @Param("example") RefFoodTimeExample example);

    int updateByExample(@Param("record") RefFoodTime record, @Param("example") RefFoodTimeExample example);

    int updateByPrimaryKeySelective(RefFoodTime record);

    int updateByPrimaryKey(RefFoodTime record);

}
