package com.cpdms.mapper.dinning;

import com.cpdms.common.annotation.DataFilter;
import com.cpdms.model.dinning.BaseEmp;
import com.cpdms.model.dinning.BaseEmpExample;
import java.util.List;
import org.apache.ibatis.annotations.Param;

/**
 * 员工信息，云闪付给数据 BaseEmpMapper
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-10-24 16:51:18
 */
public interface BaseEmpMapper {

    @DataFilter(user = false, subDept = true, deptId="be_dept_id")
    long countByExample(BaseEmpExample example);

    int deleteByExample(BaseEmpExample example);

    int deleteByPrimaryKey(String id);

    int insert(BaseEmp record);

    int insertSelective(BaseEmp record);

    @DataFilter(user = false, subDept = true, deptId="be_dept_id")
    List<BaseEmp> selectByExample(BaseEmpExample example);

    BaseEmp selectByPrimaryKey(String id);

    int updateByExampleSelective(@Param("record") BaseEmp record, @Param("example") BaseEmpExample example);

    int updateByExample(@Param("record") BaseEmp record, @Param("example") BaseEmpExample example);

    int updateByPrimaryKeySelective(BaseEmp record);

    int updateByPrimaryKey(BaseEmp record);

    @DataFilter(user = false, subDept = true, deptId="be_dept_id")
    BaseEmp selectByCardNo(String cardNo);

    @DataFilter(user = false, subDept = true, deptId="be_dept_id")
    BaseEmp selectByEncryptCardNo(String encryptCardNo);

    @DataFilter(user = false, subDept = true, deptId="be_dept_id")
    BaseEmp selectByOpenId(String openId);

}
