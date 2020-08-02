package com.cpdms.mapper.examination;

import java.util.List;

import com.cpdms.model.examination.BizExaminationOrdItem;
import com.cpdms.model.examination.BizExaminationOrdItemExample;
import org.apache.ibatis.annotations.Param;

/**
 * 体检预约项目表 BizExaminationOrdItemMapper
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-11-18 11:57:03
 */
public interface BizExaminationOrdItemMapper {

    long countByExample(BizExaminationOrdItemExample example);

    int deleteByExample(BizExaminationOrdItemExample example);

    int deleteByPrimaryKey(String id);

    int insert(BizExaminationOrdItem record);

    int insertSelective(BizExaminationOrdItem record);

    List<BizExaminationOrdItem> selectByExample(BizExaminationOrdItemExample example);

    BizExaminationOrdItem selectByPrimaryKey(String id);

    int updateByExampleSelective(@Param("record") BizExaminationOrdItem record, @Param("example") BizExaminationOrdItemExample example);

    int updateByExample(@Param("record") BizExaminationOrdItem record, @Param("example") BizExaminationOrdItemExample example);

    int updateByPrimaryKeySelective(BizExaminationOrdItem record);

    int updateByPrimaryKey(BizExaminationOrdItem record);

}
