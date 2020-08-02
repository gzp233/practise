package com.cpdms.mapper.examination;

import java.util.List;

import com.cpdms.model.examination.RefExaminationItem;
import com.cpdms.model.examination.RefExaminationItemExample;
import org.apache.ibatis.annotations.Param;

/**
 *  RefExaminationItemMapper
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-12-07 10:10:36
 */
public interface RefExaminationItemMapper {

    long countByExample(RefExaminationItemExample example);

    int deleteByExample(RefExaminationItemExample example);

    int deleteByPrimaryKey(String id);

    int insert(RefExaminationItem record);

    int insertSelective(RefExaminationItem record);

    List<RefExaminationItem> selectByExample(RefExaminationItemExample example);

    RefExaminationItem selectByPrimaryKey(String id);

    int updateByExampleSelective(@Param("record") RefExaminationItem record, @Param("example") RefExaminationItemExample example);

    int updateByExample(@Param("record") RefExaminationItem record, @Param("example") RefExaminationItemExample example);

    int updateByPrimaryKeySelective(RefExaminationItem record);

    int updateByPrimaryKey(RefExaminationItem record);

}
