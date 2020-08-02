package com.cpdms.service.examination;

import java.util.Date;
import java.util.List;

import com.cpdms.common.base.BaseService;
import com.cpdms.common.base.PageInfo;
import com.cpdms.common.support.Convert;
import com.cpdms.mapper.examination.BaseExaminationMapper;
import com.cpdms.mapper.examination.RefExaminationItemMapper;
import com.cpdms.model.custom.Tablepar;
import com.cpdms.model.examination.BaseExamination;
import com.cpdms.model.examination.BaseExaminationExample;
import com.cpdms.model.examination.RefExaminationItem;
import com.cpdms.model.examination.RefExaminationItemExample;
import com.cpdms.util.SnowflakeIdWorker;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import com.github.pagehelper.PageHelper;
import org.springframework.transaction.annotation.Transactional;

/**
 * 基础体检项目表 BaseExaminationService
 *
 * @author Eric_自动生成
 * @Title: BaseExaminationService.java 
 * @Package com.cpdms.hair.service 
 * @email eric@gmail.com
 * @date 2019-11-18 11:52:40  
 **/
@Service
public class BaseExaminationService implements BaseService<BaseExamination, BaseExaminationExample> {
    @Autowired
    private BaseExaminationMapper baseExaminationMapper;
    @Autowired
    private RefExaminationItemMapper refExaminationItemMapper;


    /**
     * 分页查询
     */
    public PageInfo<BaseExamination> list(Tablepar tablepar, BaseExamination baseExamination) {

        PageHelper.startPage(tablepar.getPageNum(), tablepar.getPageSize());
        List<BaseExamination> list = baseExaminationMapper.getList(baseExamination);
        PageInfo<BaseExamination> pageInfo = new PageInfo<BaseExamination>(list);
        return pageInfo;
    }

    @Override
    @Transactional
    public int deleteByPrimaryKey(String ids) {

        List<String> lista = Convert.toListStrArray(ids);
        BaseExaminationExample example = new BaseExaminationExample();
        example.createCriteria().andIdIn(lista);
        BaseExamination baseExamination = new BaseExamination();
        baseExamination.setStatus(0);
        RefExaminationItemExample refExaminationItemExample = new RefExaminationItemExample();
        refExaminationItemExample.createCriteria().andExaminationIdIn(lista);
        refExaminationItemMapper.deleteByExample(refExaminationItemExample);
        return baseExaminationMapper.updateByExampleSelective(baseExamination, example);


    }


    @Override
    public BaseExamination selectByPrimaryKey(String id) {

        return baseExaminationMapper.getOne(id);

    }


    @Override
    public int updateByPrimaryKeySelective(BaseExamination record) {
        record.setUpdateDate(new Date());
        return baseExaminationMapper.updateByPrimaryKeySelective(record);
    }

    @Transactional
    public int updateByPrimaryKeySelective(BaseExamination record, List<String> itemIds) {
        record.setUpdateDate(new Date());
        deleteMiddleTable(record);
        insertMiddleTable(record, itemIds);
        return baseExaminationMapper.updateByPrimaryKeySelective(record);
    }


    /**
     * 添加
     */
    @Override
    public int insertSelective(BaseExamination record) {
        //添加雪花主键id
        record.setId(SnowflakeIdWorker.getUUID());


        return baseExaminationMapper.insertSelective(record);
    }

    /**
     * 添加
     */
    @Transactional
    public int insertSelective(BaseExamination record, List<String> itemIds) {
        //添加雪花主键id
        record.setId(SnowflakeIdWorker.getUUID());
        int b = baseExaminationMapper.insertSelective(record);
        insertMiddleTable(record, itemIds);

        return b;
    }

    /**
	 * 关联表添加
	 */
	public void insertMiddleTable(BaseExamination record, List<String> itemIds) {
        for (String itemId : itemIds) {
            RefExaminationItem refExaminationItem=new RefExaminationItem(SnowflakeIdWorker.getUUID(),record.getId(), itemId);
            refExaminationItemMapper.insertSelective(refExaminationItem);
        }
	}

	/**
	 * 关联表删除
	 */
	public void deleteMiddleTable(BaseExamination record) {
        RefExaminationItemExample example = new RefExaminationItemExample();
        example.createCriteria().andExaminationIdEqualTo(record.getId());
        refExaminationItemMapper.deleteByExample(example);
	}


    @Override
    public int updateByExampleSelective(BaseExamination record, BaseExaminationExample example) {

        return baseExaminationMapper.updateByExampleSelective(record, example);
    }


    @Override
    public int updateByExample(BaseExamination record, BaseExaminationExample example) {

        return baseExaminationMapper.updateByExample(record, example);
    }

    @Override
    public List<BaseExamination> selectByExample(BaseExaminationExample example) {

        return baseExaminationMapper.selectByExample(example);
    }


    @Override
    public long countByExample(BaseExaminationExample example) {

        return baseExaminationMapper.countByExample(example);
    }


    @Override
    public int deleteByExample(BaseExaminationExample example) {

        return baseExaminationMapper.deleteByExample(example);
    }

    /**
     * 检查name
     *
     * @param baseExamination
     * @return
     */
    public int checkNameUnique(BaseExamination baseExamination) {
        BaseExaminationExample example = new BaseExaminationExample();
        example.createCriteria().andExaminationNameEqualTo(baseExamination.getExaminationName());
        List<BaseExamination> list = baseExaminationMapper.selectByExample(example);
        return list.size();
    }


}
