package com.cpdms.mapper.hair;

import com.cpdms.common.annotation.DataFilter;
import com.cpdms.model.hair.BizHairOrder;
import com.cpdms.model.hair.BizHairOrderExample;
import java.util.List;
import java.util.Map;

import org.apache.ibatis.annotations.Param;

/**
 * 美发预定订单表 BizHairOrderMapper
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-11-14 10:55:57
 */
public interface BizHairOrderMapper {

    @DataFilter(user = false, subDept = true, deptId="bho_dept_id")
    long countByExample(BizHairOrderExample example);

    int deleteByExample(BizHairOrderExample example);

    int deleteByPrimaryKey(String id);

    int insert(BizHairOrder record);

    int insertSelective(BizHairOrder record);

    @DataFilter(user = false, subDept = true, deptId="bho_dept_id")
    List<BizHairOrder> selectByExample(BizHairOrderExample example);

    @DataFilter(user = false, subDept = true, deptId="bho_dept_id")
    List<BizHairOrder> getOrderList(BizHairOrder bizHairOrder);

    BizHairOrder selectByPrimaryKey(String id);

    int updateByExampleSelective(@Param("record") BizHairOrder record, @Param("example") BizHairOrderExample example);

    int updateByExample(@Param("record") BizHairOrder record, @Param("example") BizHairOrderExample example);

    int updateByPrimaryKeySelective(BizHairOrder record);

    int updateByPrimaryKey(BizHairOrder record);

}
