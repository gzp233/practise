package com.cpdms.service.examination;

import java.util.List;

import com.cpdms.common.base.BaseService;
import com.cpdms.common.base.PageInfo;
import com.cpdms.common.support.Convert;
import com.cpdms.mapper.examination.BizExaminationOrdItemMapper;
import com.cpdms.model.custom.Tablepar;
import com.cpdms.model.examination.BizExaminationOrdItem;
import com.cpdms.model.examination.BizExaminationOrdItemExample;
import com.cpdms.util.SnowflakeIdWorker;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import com.github.pagehelper.PageHelper;

/**
 * 体检预约项目表 BizExaminationOrdItemService
 * @Title: BizExaminationOrdItemService.java 
 * @Package com.cpdms.hair.service 
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-11-18 11:57:03  
 **/
@Service
public class BizExaminationOrdItemService implements BaseService<BizExaminationOrdItem, BizExaminationOrdItemExample> {
	@Autowired
	private BizExaminationOrdItemMapper bizExaminationOrdItemMapper;


	/**
	 * 分页查询
	 * @param pageNum
	 * @param pageSize
	 * @return
	 */
	 public PageInfo<BizExaminationOrdItem> list(Tablepar tablepar, String name){
	        BizExaminationOrdItemExample testExample=new BizExaminationOrdItemExample();
	        testExample.setOrderByClause("id ASC");
	        if(name!=null&&!"".equals(name)){
	        	testExample.createCriteria().andExaminationNameLike("%"+name+"%");
	        }

	        PageHelper.startPage(tablepar.getPageNum(), tablepar.getPageSize());
	        List<BizExaminationOrdItem> list= bizExaminationOrdItemMapper.selectByExample(testExample);
	        PageInfo<BizExaminationOrdItem> pageInfo = new PageInfo<BizExaminationOrdItem>(list);
	        return  pageInfo;
	 }

	@Override
	public int deleteByPrimaryKey(String ids) {

			List<String> lista= Convert.toListStrArray(ids);
			BizExaminationOrdItemExample example=new BizExaminationOrdItemExample();
			example.createCriteria().andIdIn(lista);
			return bizExaminationOrdItemMapper.deleteByExample(example);


	}


	@Override
	public BizExaminationOrdItem selectByPrimaryKey(String id) {

			return bizExaminationOrdItemMapper.selectByPrimaryKey(id);

	}


	@Override
	public int updateByPrimaryKeySelective(BizExaminationOrdItem record) {
		return bizExaminationOrdItemMapper.updateByPrimaryKeySelective(record);
	}


	/**
	 * 添加
	 */
	@Override
	public int insertSelective(BizExaminationOrdItem record) {
					//添加雪花主键id
			record.setId(SnowflakeIdWorker.getUUID());


		return bizExaminationOrdItemMapper.insertSelective(record);
	}


	@Override
	public int updateByExampleSelective(BizExaminationOrdItem record, BizExaminationOrdItemExample example) {

		return bizExaminationOrdItemMapper.updateByExampleSelective(record, example);
	}


	@Override
	public int updateByExample(BizExaminationOrdItem record, BizExaminationOrdItemExample example) {

		return bizExaminationOrdItemMapper.updateByExample(record, example);
	}

	@Override
	public List<BizExaminationOrdItem> selectByExample(BizExaminationOrdItemExample example) {

		return bizExaminationOrdItemMapper.selectByExample(example);
	}


	@Override
	public long countByExample(BizExaminationOrdItemExample example) {

		return bizExaminationOrdItemMapper.countByExample(example);
	}


	@Override
	public int deleteByExample(BizExaminationOrdItemExample example) {

		return bizExaminationOrdItemMapper.deleteByExample(example);
	}

	/**
	 * 检查name
	 * @param bizExaminationOrdItem
	 * @return
	 */
	public int checkNameUnique(BizExaminationOrdItem bizExaminationOrdItem){
		BizExaminationOrdItemExample example=new BizExaminationOrdItemExample();
		example.createCriteria().andExaminationNameEqualTo(bizExaminationOrdItem.getExaminationName());
		List<BizExaminationOrdItem> list=bizExaminationOrdItemMapper.selectByExample(example);
		return list.size();
	}


}
