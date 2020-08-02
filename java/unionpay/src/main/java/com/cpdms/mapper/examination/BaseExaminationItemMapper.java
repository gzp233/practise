package com.cpdms.mapper.examination;

import com.cpdms.model.examination.BaseExaminationItem;
import com.cpdms.model.examination.BaseExaminationItemExample;
import java.util.List;
import org.apache.ibatis.annotations.Param;

/**
 *  BaseExaminationItemMapper
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-12-07 10:10:02
 */
public interface BaseExaminationItemMapper {

    long countByExample(BaseExaminationItemExample example);

    int deleteByExample(BaseExaminationItemExample example);

    int deleteByPrimaryKey(String id);

    int insert(BaseExaminationItem record);

    int insertSelective(BaseExaminationItem record);

    List<BaseExaminationItem> selectByExample(BaseExaminationItemExample example);

    BaseExaminationItem selectByPrimaryKey(String id);

    int updateByExampleSelective(@Param("record") BaseExaminationItem record, @Param("example") BaseExaminationItemExample example);

    int updateByExample(@Param("record") BaseExaminationItem record, @Param("example") BaseExaminationItemExample example);

    int updateByPrimaryKeySelective(BaseExaminationItem record);

    int updateByPrimaryKey(BaseExaminationItem record);

}
