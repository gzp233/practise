package com.cpdms.mapper.hair;

import java.util.List;

import com.cpdms.common.annotation.DataFilter;
import com.cpdms.model.hair.BaseHairSrv;
import com.cpdms.model.hair.BaseHairSrvExample;
import org.apache.ibatis.annotations.Param;

/**
 * 服务类型表 BaseHairSrvMapper
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-11-14 15:26:29
 */
public interface BaseHairSrvMapper {

    @DataFilter(user = false, subDept = true, deptId="bhs_dept_id")
    long countByExample(BaseHairSrvExample example);

    int deleteByExample(BaseHairSrvExample example);

    int deleteByPrimaryKey(String id);

    int insert(BaseHairSrv record);

    int insertSelective(BaseHairSrv record);

    @DataFilter(user = false, subDept = true, deptId="bhs_dept_id")
    List<BaseHairSrv> selectByExample(BaseHairSrvExample example);

    BaseHairSrv selectByPrimaryKey(String id);

    int updateByExampleSelective(@Param("record") BaseHairSrv record, @Param("example") BaseHairSrvExample example);

    int updateByExample(@Param("record") BaseHairSrv record, @Param("example") BaseHairSrvExample example);

    int updateByPrimaryKeySelective(BaseHairSrv record);

    int updateByPrimaryKey(BaseHairSrv record);

}
