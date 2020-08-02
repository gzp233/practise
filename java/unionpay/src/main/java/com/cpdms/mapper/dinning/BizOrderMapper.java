package com.cpdms.mapper.dinning;

import com.cpdms.common.annotation.DataFilter;
import com.cpdms.model.dinning.BizOrder;
import com.cpdms.model.dinning.BizOrderExample;
import java.util.List;
import java.util.Map;

import org.apache.ibatis.annotations.Param;

/**
 * 预定订单 BizOrderMapper
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-10-24 16:42:45
 */
public interface BizOrderMapper {

    @DataFilter(user = false, subDept = true, deptId="bo_dept_id")
    long countByExample(BizOrderExample example);

    int deleteByExample(BizOrderExample example);

    int deleteByPrimaryKey(String id);

    int insert(BizOrder record);

    int insertSelective(BizOrder record);

    @DataFilter(user = false, subDept = true, deptId="bo_dept_id")
    List<BizOrder> selectByExample(BizOrderExample example);

    @DataFilter(user = false, subDept = true, deptId="bo_dept_id")
    List<BizOrder> selectOrdersByEmpIdAndStatus(@Param("ordEmpId")String ordEmpId, @Param("type")Integer type, @Param("sellTypeName")String sellTypeName);

    BizOrder selectByPrimaryKey(String id);

    @DataFilter(user = false, subDept = true, deptId="bo_dept_id")
    BizOrder selectByOrdCode(String ordCode);

    BizOrder selectDetail(String id);

    int updateByExampleSelective(@Param("record") BizOrder record, @Param("example") BizOrderExample example);

    int updateByExample(@Param("record") BizOrder record, @Param("example") BizOrderExample example);

    int updateByPrimaryKeySelective(BizOrder record);

    @DataFilter(user = false, subDept = true, deptId="bo_dept_id")
    Map<String,Object> getMaxTakeNo(String planedTakeTime);

    int updateByPrimaryKey(BizOrder record);

    @DataFilter(user = false, subDept = true, deptId="bo_dept_id")
    public List<Map<String,Object>> queryOrdFoodSumByTime(Map<String,Object> parmas);
    @DataFilter(user = false, subDept = true, deptId="bo_dept_id")
    public List<Map<String,Object>> queryOrdPrintList(Map<String,Object> parmas);
    @DataFilter(user = false, subDept = true, deptId="bo_dept_id")
    public List<Map<String,Object>> queryOrdDetailList(Map<String,Object> parmas);


}
